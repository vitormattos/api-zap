<?php

namespace Api;

use Api\Controller\ZapController;
use Slim\App;

class Route
{
    public static function build(App $app): void
    {
        $app->get('/api/zap-search', [ZapController::class, 'search']);
    }
}
