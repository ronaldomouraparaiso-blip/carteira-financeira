<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    /**
     * Exibir histórico de transações
     */
    public function index()
    {
        $user = Auth::user();
        $transactions = Transaction::where('sender_id', $user->id)
            ->orWhere('receiver_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('transactions.index', compact('transactions'));
    }

    /**
     * Exibir formulário de depósito
     */
    public function depositForm()
    {
        return view('transactions.deposit');
    }

    /**
     * Processar depósito
     */
    public function deposit(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'description' => 'nullable|string|max:255',
        ]);

        $user = Auth::user();
        $amount = $request->amount;

        try {
            DB::beginTransaction();

            // Se o saldo está negativo, o depósito deve acrescentar ao valor
            $user->balance = $user->balance + $amount;
            $user->save();

            // Registrar a transação
            Transaction::create([
                'receiver_id' => $user->id,
                'type' => 'deposit',
                'amount' => $amount,
                'status' => 'completed',
                'description' => $request->description ?? 'Depósito realizado',
            ]);

            DB::commit();

            return redirect()->route('transactions.index')
                ->with('success', 'Depósito realizado com sucesso! Novo saldo: R$ ' . number_format($user->balance, 2, ',', '.'));

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erro ao processar depósito: ' . $e->getMessage());
        }
    }

    /**
     * Exibir formulário de transferência
     */
    public function transferForm()
    {
        $users = User::where('id', '!=', Auth::id())->get();
        return view('transactions.transfer', compact('users'));
    }

    /**
     * Processar transferência
     */
    public function transfer(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'amount' => 'required|numeric|min:0.01',
            'description' => 'nullable|string|max:255',
        ]);

        $sender = Auth::user();
        $receiver = User::findOrFail($request->receiver_id);
        $amount = $request->amount;

        // Validar se o usuário tem saldo suficiente
        if ($sender->balance < $amount) {
            return back()->with('error', 'Saldo insuficiente para realizar a transferência. Saldo atual: R$ ' . number_format($sender->balance, 2, ',', '.'));
        }

        // Validar se não está tentando transferir para si mesmo
        if ($sender->id == $receiver->id) {
            return back()->with('error', 'Você não pode transferir para si mesmo.');
        }

        try {
            DB::beginTransaction();

            // Debitar do remetente
            $sender->balance = $sender->balance - $amount;
            $sender->save();

            // Creditar ao destinatário
            $receiver->balance = $receiver->balance + $amount;
            $receiver->save();

            // Registrar a transação
            Transaction::create([
                'sender_id' => $sender->id,
                'receiver_id' => $receiver->id,
                'type' => 'transfer',
                'amount' => $amount,
                'status' => 'completed',
                'description' => $request->description ?? 'Transferência realizada',
            ]);

            DB::commit();

            return redirect()->route('transactions.index')
                ->with('success', 'Transferência realizada com sucesso! Novo saldo: R$ ' . number_format($sender->balance, 2, ',', '.'));

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erro ao processar transferência: ' . $e->getMessage());
        }
    }

    /**
     * Reverter uma transação
     */
    public function reverse($id)
    {
        $transaction = Transaction::findOrFail($id);
        $user = Auth::user();

        // Verificar se o usuário está envolvido na transação
        if ($transaction->sender_id != $user->id && $transaction->receiver_id != $user->id) {
            return back()->with('error', 'Você não tem permissão para reverter esta transação.');
        }

        // Verificar se a transação já foi revertida
        if ($transaction->isReversed()) {
            return back()->with('error', 'Esta transação já foi revertida.');
        }

        try {
            DB::beginTransaction();

            if ($transaction->type == 'deposit') {
                // Reverter depósito: subtrair o valor do saldo do destinatário
                $receiver = User::findOrFail($transaction->receiver_id);
                $receiver->balance = $receiver->balance - $transaction->amount;
                $receiver->save();

            } elseif ($transaction->type == 'transfer') {
                // Reverter transferência: devolver o valor ao remetente e subtrair do destinatário
                $sender = User::findOrFail($transaction->sender_id);
                $receiver = User::findOrFail($transaction->receiver_id);

                $sender->balance = $sender->balance + $transaction->amount;
                $receiver->balance = $receiver->balance - $transaction->amount;

                $sender->save();
                $receiver->save();
            }

            // Atualizar status da transação
            $transaction->status = 'reversed';
            $transaction->reversed_at = now();
            $transaction->save();

            DB::commit();

            return redirect()->route('transactions.index')
                ->with('success', 'Transação revertida com sucesso! Novo saldo: R$ ' . number_format($user->fresh()->balance, 2, ',', '.'));

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erro ao reverter transação: ' . $e->getMessage());
        }
    }
}
