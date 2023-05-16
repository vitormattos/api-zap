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
* Open developer tools of your browzer
* Do a search in ZapImoveis.
* Filter on Network tap by `/listing`
* Identify the last `GET` request
* Use the same as a query string in endpoint `GET /zap-search`

## Analize collected data
```sql
select u.zap_id,
       u.title,
       u.bedrooms,
       u.bathrooms,
       u.total_areas,
       ut."type",
       p.price,
       p.yearly_iptu,
       p.monthly_condo_fee,
       p.monthly_rental_total_price as total,
       a.street,
       a.neighborhood,
       concat('https://www.zapimoveis.com.br/imovel/', u.zap_id) as url
  from unit u
  join prices p on p.zap_id = u.zap_id
  join unit_type ut on ut.zap_id = u.zap_id
  join address a on a.zap_id = u.zap_id
 where p.business_type in ('RENTAL')
   and u.zap_id not in ('2559137018')
   and ut.type not in ('OFFICE', 'COMMERCIAL_BUILDING', 'COMMERCIAL_PROPERTY', 'BUSINESS', 'PARKING_SPACE', 'SHED_DEPOSIT_WAREHOUSE')
   and a.street not in ('Rua dos Bobos')
   and (u.bedrooms >= 2 or u.bedrooms is null)
   and (u.total_areas >= 80 or u.total_areas is null)
--   and a.neighborhood in ('')
 order by p.monthly_rental_total_price
```