# API REST to collect ZapImoveis data

Simple REST API to colect ZapImoveis data

## Install
```
cp .env.example .env
docker compose up -d
docker compose exec api composer install
docker compose exec api vendor/bin/phinx migrage
```

## Endpoints
/api/zap-search

### Query string
Do a query string in ZapImoveis and use the same as a query string