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
* Use the same as a query string

## Analize collected data
```sql
select u.zap_id,
       u.title,
       u.bedrooms,
       u.bathrooms,
       u.total_areas,
       ut.type,
       p.price,
       p.yearly_iptu,
       p.monthly_condo_fee as condominio,
       p.monthly_rental_total_price as total,
       a.street as address,
       a.neighborhood,
       a.city,
       u.created_at,
       u.updated_at,
       concat('https://www.zapimoveis.com.br/imovel/', u.zap_id) as url,
       concat(a.lat, ' ', a.lon) as latlong,
       data ->> 'whatsappNumber' as whatsapp,
       u.data
  from unit u
  join prices p on p.zap_id = u.zap_id
  join unit_type ut on ut.zap_id = u.zap_id
  join address a on a.zap_id = u.zap_id
 where business_type in ('RENTAL')
   and u.zap_id not in ('2615584494')
   -- Comerciais
   and ut.type not in ('OFFICE', 'COMMERCIAL_BUILDING', 'COMMERCIAL_PROPERTY', 'BUSINESS', 'PARKING_SPACE', 'SHED_DEPOSIT_WAREHOUSE')
   -- Localização ruim
   and a.street not in ('Rua dos Bobos')
   and (u.bedrooms >= 3 or u.bedrooms is null)
   and u.data ->> 'description' not like '%mobilhado%'
   and p.monthly_rental_total_price <= 2700
   and (u.created_at >= '2023-06-18' or u.updated_at >= '2023-06-18')
--   and (u.total_areas >= 80 or u.total_areas is null)
   and a.neighborhood in ('Bairro Legal')
   and a.city = 'Rio de Janeiro'
   order by total
```