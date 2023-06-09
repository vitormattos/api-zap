<?php

namespace Api\Service;

use Api\Mapper\ZapMapper;
use GuzzleHttp\Client;

class ZapService
{
    private int $pageSize = 100;
    private ZapMapper $zapMapper;

    public function __construct()
    {
        $this->zapMapper = new ZapMapper();
    }

    public function getZap(array $query, int $page = 1, int $from = 0): void
    {
        $query = array_merge(
            [
                'business' => 'RENTAL',
                'listingType' => 'USED',
                'portal' => 'ZAP',
                'bedrooms' => null,
                'sort' => 'pricingInfos.price ASC sortFilter:pricingInfos.businessType=\'RENTAL\'',
                'usableAreasMin' => 70,
                'priceMax' => 5000,
                'categoryPage' => 'RESULT',
                'addressCountry' => '',
                'addressState' => 'Rio de Janeiro',
                'addressCity' => 'Rio de Janeiro',
                'addressZone' => 'Zona Central',
                'addressNeighborhood' => 'Centro',
                'addressStreet' => '',
                'addressAccounts' => '',
                'addressType' => 'neighborhood',
                'levels' => 'NEIGHBORHOOD',
                'size' => $this->pageSize,
                'includeFields' => $this->getIncludeFields(),
            ],
            $query,
            [
                'from' => $from,
                'page' => $page,
            ]
        );

        $options = [
            'headers' => [
                'X-Domain' => 'www.zapimoveis.com.br',
            ],
            'stream' => true,
            'version' => '1.0',
        ];

        $client = new Client();
        $response = $client->get('https://glue-api.zapimoveis.com.br/v2/listings?' . http_build_query($query), $options);
        $content = $response->getBody()->getContents();
        $decoded = json_decode($content, true);
        $this->zapMapper->saveData($decoded['search']['result']['listings']);
        if ($page * $query['page'] < $decoded['search']['totalCount']) {
            $page++;
            $from += $this->pageSize;
            $this->getZap($query, $page, $from);
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
