<?php

namespace Api\Service;

use Api\Mapper\ZapMapper;
use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface;

class ZapService
{
    private int $pageSize = 100;
    private ZapMapper $zapMapper;
    private array $options = [
        'headers' => [
            'X-Domain' => 'www.zapimoveis.com.br',
        ],
        'stream' => true,
        'version' => '1.0',
    ];

    public function __construct()
    {
        $this->zapMapper = new ZapMapper();
    }

    public function getZap(array $query, int $page = 1, int $from = 0, bool $syncronous = true): void
    {
        $client = new Client();
        if ($syncronous) {
            $this->getSync($query, $page, $from);
        } else {
            $this->getAssync($query, $page, $from);
        }
    }

    private function getUrl(array $query, $from, $page): string
    {
        $query = array_merge(
            [
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
        $url = 'https://glue-api.zapimoveis.com.br/v2/listings?' . urldecode(http_build_query($query));
        return $url;
    }

    private function getSync(array $query, int $page, int $from): void
    {
        $client = new Client();
        $url = $this->getUrl($query, $from, $page);
        $response = $client->get($url, $this->options);
        $this->processResponse($response, $query, $page, $from);
    }

    private function getAssync(array $query, int $page, int $from): void
    {
        $client = new Client();
        $url = $this->getUrl($query, $from, $page);
        $promisse = $client->getAsync($url, $this->options);
        $promisse->then(
            function (ResponseInterface $response) use ($query, $page, $from) {
                $this->processResponse($response, $query, $page, $from);
            }
        );
    }

    private function processResponse(ResponseInterface $response, array $query, int $page, int $from): void
    {
        $content = $response->getBody()->getContents();
        $decoded = json_decode($content, true);
        $total = $decoded['search']['totalCount'];
        if ($page * $query['page'] < $total) {
            $page++;
            $from += $this->pageSize;
            $this->getZap($query, $page, $from, false);
        }
        $this->zapMapper->saveData($decoded['search']['result']['listings']);
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
