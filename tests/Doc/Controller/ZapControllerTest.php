<?php

namespace Tests\Doc\Controller;

use ByJG\ApiTools\ApiRequester;
use Tests\Doc\ApiTestCase;

final class ZapControllerTest extends ApiTestCase
{
    public function testRequestGetWithEmptyDatabase(): void
    {
        $request = new ApiRequester();
        $request
            ->withMethod('GET')
            ->withPath('/zap-search');
        $this->assertRequest($request);
    }
}
