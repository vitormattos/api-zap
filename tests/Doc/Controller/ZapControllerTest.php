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
            ->withPath('/zap-search')
            ->withQuery([
                'viewport' => '-43.27229506098763,-22.897444415987277|-43.2885170611097,-22.90769809054536'
            ]);
        $this->assertRequest($request);
    }
}
