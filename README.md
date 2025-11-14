# 💳 Carteira Financeira Laravel com Docker

Este projeto implementa uma carteira financeira básica com funcionalidades de cadastro, autenticação, depósito, transferência e reversão de transações, utilizando Laravel, MySQL e Docker Compose.

## 🚀 Funcionalidades

- **Autenticação e Cadastro:** Utiliza Laravel Breeze para um sistema de autenticação robusto.
- **Depósito:** Permite adicionar saldo à conta. O sistema trata saldos negativos, adicionando o valor depositado ao saldo atual.
- **Transferência:** Permite transferir saldo entre usuários, com validação de saldo suficiente antes da transação.
- **Reversão de Transação:** Permite reverter depósitos e transferências, ajustando os saldos dos usuários envolvidos e marcando a transação como revertida.
- **Consistência de Dados:** Todas as operações financeiras são executadas dentro de transações de banco de dados para garantir atomicidade e integridade.

## 🛠️ Tecnologias

- **Framework:** Laravel 10+
- **Linguagem:** PHP 8.2+
- **Banco de Dados:** MySQL 8.0
- **Ambiente:** Docker Compose (Nginx + PHP-FPM + MySQL)
- **Frontend:** Blade, Tailwind CSS (via Laravel Breeze)

## 📦 Instalação e Configuração

**IMPORTANTE:** Este projeto utiliza uma arquitetura Docker mais estável (Nginx + PHP-FPM). Siga os passos abaixo com atenção.

### 1. Pré-requisitos

Certifique-se de ter o **Docker** e o **Docker Compose** instalados em sua máquina.

### 2. Configuração Inicial

O arquivo `.env` já está configurado para o ambiente Docker.

### 3. Construa e Inicie os Containers

Como o `Dockerfile` foi alterado, é necessário reconstruir a imagem do container `app`.

```bash
# 1. Construa a imagem do container 'app'
docker-compose build app

# 2. Inicie todos os containers
docker-compose up -d
```

### 4. Geração da Chave e Migrations

É **crucial** gerar a chave da aplicação e executar as migrations para configurar o banco de dados.

```bash
# 1. Gerar a chave da aplicação (APP_KEY)
docker-compose exec app php artisan key:generate

# 2. Executar as migrations
docker-compose exec app php artisan migrate
```

### 5. Acesse a Aplicação

Abra seu navegador e acesse:

- **Aplicação**: `http://localhost:8001`
- **phpMyAdmin**: `http://localhost:8081`

## ⚙️ Comandos Úteis

| Comando | Descrição |
| :--- | :--- |
| `docker-compose up -d` | Inicia os containers em modo detached. |
| `docker-compose down` | Para e remove os containers. |
| `docker-compose build app` | Reconstrói a imagem do container `app`. |
| `docker-compose exec app bash` | Acessa o terminal do container da aplicação. |
| `docker-compose exec app php artisan <comando>` | Executa comandos Artisan no container. |
| `docker-compose ps` | Verifica o status dos containers. |

---
Para mais detalhes e solução de problemas, consulte o arquivo `INSTALACAO.md`.
