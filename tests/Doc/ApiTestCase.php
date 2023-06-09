<?php

namespace Tests\Doc;

use ByJG\ApiTools\ApiTestCase as ApiToolsApiTestCase;
use ByJG\ApiTools\OpenApi\OpenApiSchema;
use Symfony\Component\Yaml\Yaml;

class ApiTestCase extends ApiToolsApiTestCase {

    public function setUp(): void
    {
        parent::setUp();
        $data = Yaml::parse(file_get_contents('doc/Zap.yaml'));
        if (getenv('API_PORT')) {
            $data['servers'][0]['url'] = 'http://localhost:'.getenv('API_PORT') . '/api';
        }
        $schema = OpenApiSchema::getInstance($data);
        $this->setSchema($schema);

        $this->cleanDatabase();
    }

    private function cleanDatabase(): void
    {
        $databasePath = 'storage/database.json';
        if (file_exists($databasePath) && is_writable($databasePath)) {
            unlink($databasePath);
        }
    }
}
