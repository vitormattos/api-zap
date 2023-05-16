<?php

namespace Api\Mapper;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\DBAL\ParameterType;
use Doctrine\DBAL\Query\QueryBuilder;

class ZapMapper
{
    private Connection $conn;

    public function __construct()
    {
        $this->conn = DriverManager::getConnection([
            'dbname' => (string) getenv('DB_NAME'),
            'user' => (string) getenv('DB_USER'),
            'password' => (string) getenv('DB_PASSWORD'),
            'host' => (string) getenv('DB_HOST'),
            'driver' => (string) getenv('DP_DRIVER'),
        ]);
    }

    public function saveData(array $data): void
    {
        foreach ($data as $row) {
            $this->insertItem($row['listing']);
        }
    }

    private function getQueryBuilder(): QueryBuilder
    {
        return new QueryBuilder($this->conn);
    }

    private function insertItem(array $row): void
    {
        $qb = $this->getQueryBuilder();
        $data = [
            'data' => $qb->createNamedParameter(json_encode($row)),
            'zap_id' => $qb->createNamedParameter($row['id']),
            'title' => $qb->createNamedParameter($row['title']),
        ];
        if (array_key_exists(0, $row['bedrooms'])) {
            $data['bedrooms'] = $qb->createNamedParameter($row['bedrooms'][0], ParameterType::INTEGER);
        }
        if (array_key_exists(0, $row['bathrooms'])) {
            $data['bathrooms'] = $qb->createNamedParameter($row['bathrooms'][0], ParameterType::INTEGER);
        }
        if (array_key_exists(0, $row['totalAreas'])) {
            $data['total_areas'] = $qb->createNamedParameter($row['totalAreas'][0], ParameterType::INTEGER);
        }
        try {
            $qb->insert('unit')
                ->values($data)
                ->executeStatement();
            if (array_key_exists('address', $row)) {
                $this->insertAddress($row['address'], $row['id']);
            }
            $this->insertPrices($row);
            $this->insertTypes($row);
        } catch (UniqueConstraintViolationException $th) {
        }
    }

    private function insertTypes($row): void
    {
        foreach ($row['unitTypes'] as $unitType) {
            $qb = $this->getQueryBuilder();
            $qb->insert('unit_type')
                ->values([
                    'zap_id' => $qb->createNamedParameter($row['id']),
                    'type' => $qb->createNamedParameter($unitType)
                ])
                ->executeStatement();
        }
    }

    private function insertAddress(array $address, string $zapId): void
    {
        $qb = $this->getQueryBuilder();
        $fields = [
            'city' => 'city',
            'name' => 'name',
            'pois' => 'pois',
            'zone' => 'zone',
            'level' => 'level',
            'state' => 'state',
            'source' => 'source',
            'street' => 'street',
            'country' => 'country',
            'neighborhood' => 'neighborhood',
            'state_acronym' => 'stateAcronym',
            'complement' => 'complement',
            'precision' => 'precision',
            'zip_code' => 'zipCde',
        ];
        $data = [
            'zap_id' => $qb->createNamedParameter($zapId)
        ];
        foreach ($fields as $col => $original) {
            if (array_key_exists($original, $address) && !empty($address[$original])) {
                $data[$col] = $qb->createNamedParameter($address[$original]);
            }
        }
        $qb->insert('address')
            ->values($data)
            ->executeStatement();
    }

    private function insertPrices(array $row): void
    {
        $qb = $this->getQueryBuilder();
        foreach ($row['pricingInfos'] as $price) {
            $data = [
                'zap_id' => $qb->createNamedParameter($row['id']),
                'price' => $qb->createNamedParameter($price['price']),
                'business_type' => $qb->createNamedParameter($price['businessType']),
            ];
            if (array_key_exists('monthlyCondoFee', $price)) {
                $data['monthly_condo_fee'] = $qb->createNamedParameter(
                    $price['monthlyCondoFee'],
                    ParameterType::INTEGER
                );
            }
            if (array_key_exists('yearlyIptu', $price)) {
                $data['yearly_iptu'] = $qb->createNamedParameter($price['yearlyIptu'], ParameterType::INTEGER);
            }
            if (array_key_exists('rentalInfo', $price)) {
                if (!empty($price['rentalInfo']['period'])) {
                    $data['period'] = $qb->createNamedParameter($price['rentalInfo']['period']);
                }
                $data['warranties'] = $qb->createNamedParameter(json_encode($price['rentalInfo']['warranties']));
                if (array_key_exists('monthlyRentalTotalPrice', $price['rentalInfo'])) {
                    $data['monthly_rental_total_price'] = $qb->createNamedParameter(
                        $price['rentalInfo']['monthlyRentalTotalPrice'],
                        ParameterType::INTEGER
                    );
                }
            }
            $qb->insert('prices')
                ->values($data)
                ->executeStatement();
        }
    }
}
