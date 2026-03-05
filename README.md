# Desafio PHP (Laminas + Doctrine + MySQL)

Este projeto implementa o desafio técnico usando Laminas (MVC), Doctrine ORM, Doctrine Migrations e MySQL (Docker).

## Requisitos
- Docker Desktop (com Docker Compose)
- (Opcional) DBeaver / MySQL Workbench para visualizar o banco

## Estrutura do domínio

O sistema implementa três níveis hierárquicos:

```
AC
   └── ACN2
         └── AR
```
AC
- entidade principal

ACN2
- filho de AC

AR
- filho de ACN2

## QRCode

Cada entidade (AC, ACN2, AR) possui um QRCode.

O QRCode aponta para a página de detalhes da entidade:

/acs/{id}
/ac-n2/{id}
/ars/{id}

Essas páginas exibem a hierarquia completa da entidade.

## Instalar dependências PHP

Se for a primeira execução ou se a pasta `vendor` não existir:

```bash
docker compose run --rm laminas composer install
```

## Como rodar o projeto (Docker)
1. Subir os containers:
   ```bash
   docker compose up -d --build

2. Acessar a aplicação:
   ```bash
   http://localhost:8080
   ```

## Banco de dados (MySQL)
O banco é provisionado via Docker Compose.

### Conectar no MySQL via DBeaver
1. Abrir o DBeaver
2. Clicar em "New Database Connection"
3. Selecione "MySQL"
4. Crie uma conexão com os dados:
   - Host: localhost
   - Port: 3307
   - Database: desafio_php
   - User: root
   - Password: root

        **Observação:** a porta 3307 é a porta exposta no host (Windows). Dentro do Docker, o MySQL roda na 3306.

## Doctrine (ORM + Migrations)

1. Verificar entidades mapeadas
```bash
docker compose exec laminas vendor/bin/doctrine-module orm:info
```

2. Gerar migration (primeira vez / schema vazio)
```bash
docker compose exec laminas vendor/bin/doctrine-module migrations:diff --from-empty-schema
```

3. Rodar migrations
```bash
docker compose exec laminas vendor/bin/doctrine-module migrations:migrate
```

4. Ver tabelas via CLI **(opcional)**
```bash
docker compose exec mysql mysql -uroot -proot -e "SHOW TABLES FROM desafio_php;"
```

## Criar usuário inicial (seed)
```bash
docker compose exec laminas vendor/bin/doctrine-module app:user:create --email=admin@admin.com --password=admin123
```

Se o usuário já existir e quiser sobrescrever a senha:
```bash
docker compose exec laminas vendor/bin/doctrine-module app:user:create --email=admin@admin.com --password=admin123 --force
```

Após criar o usuário, você pode fazer login na aplicação com as credenciais que você definiu ou caso não tenha definido, use as credenciais padrão:
- Email: admin@admin.com
- Senha: admin123

Página de login: http://localhost:8080/login

## Troubleshooting

### Erros de conexão com MySQL
- Verifique se os containers estão de pé:
```bash
docker compose ps
```

- Confirme as variáveis de ambiente do serviço ```laminas``` no ```docker-compose.yml```: ```DB_HOST=mysql```, ```DB_USER=root```, ```DB_PASSWORD=root```, ```DB_NAME=desafio_php```.

### Extensão pdo_mysql ausente
Se o container PHP não listar ```pdo_mysql``` em ```php -m```, é necessário habilitar a extensão no Dockerfile e rebuildar:
```bash
docker compose down
docker compose up -d --build
```

### QRCode não gera imagem (erro ext-gd)

Se aparecer o erro:
```bash
ext-gd not loaded
```

É necessário habilitar a extensão GD no Dockerfile e rebuildar o container:

```bash
docker compose down
docker compose up -d --build
```

### Doctrine Proxy Directory
Se aparecer o erro:
```bash
proxy directory must be writable
```

Garanta que a pasta existe e tem permissão:

```bash
docker compose exec laminas sh -lc "mkdir -p data/DoctrineORMModule/Proxy && chmod -R 777 data/DoctrineORMModule"
```
