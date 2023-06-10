<?php

namespace Api\Mapper;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\DBAL\ParameterType;
use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\DBAL\Types\Types;

class ZapMapper
{
    private Connection $conn;

    public function __construct()
    {
        $this->conn = DriverManager::getConnection([
            'dbname' => getenv('DB_NAME'),
            'user' => getenv('DB_USER'),
            'password' => getenv('DB_PASSWORD'),
            'host' => getenv('DB_HOST'),
            'driver' => getenv('DP_DRIVER')
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

    private function stringToDateImmutable(string $date): \DateTimeImmutable
    {
        $date = preg_replace('/\.\d+Z$/', '', $date);
        $date = preg_replace('/Z$/', '', $date);

        $return = \DateTimeImmutable::createFromFormat('Y-m-d\TH:i:s', $date);
        return $return;
    }

    private function insertItem(array $row): void
    {
        $qb = $this->getQueryBuilder();
        $data = [
            'data' => $qb->createNamedParameter(json_encode($row)),
            'zap_id' => $qb->createNamedParameter($row['id']),
            'title' => $qb->createNamedParameter($row['title']),
            'created_at' => $qb->createNamedParameter(
                $this->stringToDateImmutable($row['createdAt']),
                Types::DATE_IMMUTABLE
            ),
        ];
        if (!empty($row['updatedAt'])) {
            $data['updated_at'] = $qb->createNamedParameter(
                $this->stringToDateImmutable($row['updatedAt']),
                Types::DATE_IMMUTABLE
            );
        }
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
            'zip_code' => 'zipCode',
        ];
        $data = [
            'zap_id' => $qb->createNamedParameter($zapId)
        ];
        foreach ($fields as $col => $original) {
            if (array_key_exists($original, $address) && !empty($address[$original])) {
                $data[$col] = $qb->createNamedParameter($address[$original]);
            }
        }
        if (!empty($address['point']) && !empty($address['point']['lat'] && !empty($address['point']['lon']))) {
            $data['lat'] = $address['point']['lat'];
            $data['lon'] = $address['point']['lon'];
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

    private function getIncludeFields(): string
    {
        $array = [
            'search' => [
                'result' => [
                    'listings' => [
                        'listing' => [
                            'id',
                            'acceptExchange',
                            'address',
                            'advertiserContact',
                            'advertiserId',
                            'amenities',
                            'bathrooms',
                            'bedrooms',
                            'buildings',
                            'capacityLimit',
                            'constructionStatus',
                            'createdAt',
                            'description',
                            'displayAddressType',
                            'externalId',
                            'floors',
                            'legacyId',
                            'listingType',
                            'listingsCount',
                            'nonActivationReason',
                            'parkingSpaces',
                            'portal',
                            'priceSuggestion',
                            'pricingInfos',
                            'propertyType',
                            'providerId',
                            'publicationType',
                            'resale',
                            'showPrice',
                            'sourceId',
                            'stamps',
                            'status',
                            'suites',
                            'title',
                            'totalAreas',
                            'unitFloor',
                            'unitSubTypes',
                            'unitTypes',
                            'unitsOnTheFloor',
                            'updatedAt',
                            'usableAreas',
                            'usageTypes',
                            'whatsappNumber',
                        ],
                        'account' => [
                            'id',
                            'name',
                            'logoUrl',
                            'licenseNumber',
                            'showAddress',
                            'legacyVivarealId',
                            'legacyZapId',
                            'minisite'
                        ],
                        'medias',
                    ],
                ],
                'totalCount'
            ],
        ];
        $string = array_reduce([$array], [$this, 'reduce',]);
        return $string;
    }

    private function reduce($carry, $item): string
    {
        if (is_string($item)) {
            if ($carry) {
                return $carry . ',' . $item;
            }
            return $item;
        }
        if (is_array($item)) {
            foreach ($item as $key => $value) {
                if (is_array($value)) {
                    $item[$key] = $key . '(' . array_reduce([$value], [$this, 'reduce']) . ')';
                }
            }
        }
        $carry .= implode(',', $item);
        return $carry;
    }
}
