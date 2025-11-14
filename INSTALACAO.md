# Guia de Instalação Detalhado - Carteira Financeira

Este guia detalha os passos para configurar e executar a aplicação de carteira financeira usando Docker Compose.

## 🚀 Instalação Rápida

### 1. Pré-requisitos

Certifique-se de ter instalado:
- **Docker**
- **Docker Compose**

### 2. Configuração Inicial

O arquivo `.env` já está configurado para o ambiente Docker.

### 3. Construa e Inicie os Containers

Como o `Dockerfile` foi alterado para uma arquitetura Nginx + PHP-FPM, é necessário reconstruir a imagem do container `app`.

```bash
# 1. Construa a imagem do container 'app'
docker-compose build app

# 2. Inicie todos os containers
docker-compose up -d
```

Aguarde alguns minutos enquanto os containers são criados e iniciados.

### 4. Geração da Chave e Migrations

É **crucial** gerar a chave da aplicação (`APP_KEY`) e executar as migrations para configurar o banco de dados.

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

## 📋 Verificação da Instalação

### Verificar Status dos Containers

```bash
docker-compose ps
```
Todos os containers (`laravel_nginx`, `laravel_app`, `mysql_db`, `phpmyadmin`) devem estar no estado `Up`.

### Verificar Logs

```bash
docker-compose logs -f
```
Verifique se há erros nos logs, especialmente nos containers `laravel_app` e `laravel_nginx`.

## 🐛 Solução de Problemas Comuns

### Erro: `ERR_CONNECTION_REFUSED` ou `Connection reset by peer`

1.  **Verifique se a APP_KEY foi gerada:** Execute `docker-compose exec app php artisan key:generate`.
2.  **Verifique as Permissões:** Se o erro persistir, pode ser um problema de permissão no volume. Execute:
    ```bash
    docker-compose exec app chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache
    docker-compose exec app chmod -R 775 /var/www/storage /var/www/bootstrap/cache
    ```
3.  **Firewall:** Verifique se o firewall do seu sistema operacional não está bloqueando as portas `8001` e `8081`.

### Erro: `Service 'app' failed to build` (exit code 100)

Se o erro de build ocorrer, tente limpar o cache do Docker e reconstruir:
```bash
docker-compose down --rmi all
docker-compose build app
```

### Porta 8001 ou 8081 já em uso

Edite o arquivo `docker-compose.yml` e altere o mapeamento de portas:
```yaml
# Para a aplicação
ports:
  - "8002:80" # Altere 8001 para 8002

# Para o phpMyAdmin
ports:
  - "8082:80" # Altere 8081 para 8082
```

## 🎯 Primeiro Acesso

1.  Acesse `http://localhost:8001`.
2.  Clique em **"Register"** e crie seu primeiro usuário.
3.  Após o login, você será redirecionado para a página de transações.
4.  Faça um depósito inicial para começar a usar a carteira.
