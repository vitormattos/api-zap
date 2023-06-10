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
                'categoryPage' => 'RESULT',
                'developmentsSize' => 3,
                'includeFields' => $this->getIncludeFields(),
                'levels' => 'CITY',
                'size' => $this->pageSize,
                'sort' => 'pricingInfos.price ASC',
                'superPremiumSize' => 0,
                'usageTypes' => 'RESIDENTIAL',
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
        $url = 'https://glue-api.zapimoveis.com.br/v2/listings?' . urldecode(http_build_query($query));
        $response = $client->get($url, $options);
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
                            'contractType',
                            'createdAt',
                            'description',
                            'displayAddressType',
                            'externalId',
                            'floors',
                            'legacyId',
                            'listingsCount',
                            'listingType',
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
                            'unitsOnTheFloor',
                            'unitSubTypes',
                            'unitTypes',
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
                            'createdDate',
                            'minisite',
                            'tier'
                        ],
                        'medias',
                        'accountLink',
                        'link'
                    ],
                ],
                'totalCount'
            ],
            'page',
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
