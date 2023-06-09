<?php

namespace Api\Controller;

use Api\Service\ZapService;
use Doctrine\DBAL\Connection;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Routing\RouteContext;

class ZapController
{
    private ZapService $zapService;

    public function __construct()
    {
        $this->zapService = new ZapService();
    }

    public function search(Request $request, Response $response): Response
    {
        $this->zapService->getZap(array_merge(
            $request->getQueryParams(),
            $request->getParsedBody()
        ));
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
    }
}
