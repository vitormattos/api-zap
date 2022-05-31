<?php

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\TableNode;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;
use PhpBuiltin\RunServerListener;
use PHPUnit\Framework\Assert;
use Psr\Http\Message\ResponseInterface;

/**
 * Defines application features from the specific context.
 */
class FeatureContext implements Context
{
    private string $baseUrl;
    private ResponseInterface $response;

    public function __construct()
    {
        $this->baseUrl = RunServerListener::getServerRoot();
    }

    public function sendRequest(string $verb, string $url, ?array $body = null, array $headers = []): void
    {
        $client = new Client();

        $fullUrl = $this->baseUrl . ltrim($url, '/');

        $options['headers'] = $headers;

        if (is_array($body)) {
            $options['form_params'] = $body;
        }

        try {
            $this->response = $client->{$verb}($fullUrl, $options);
        } catch (ClientException $e) {
            $this->response = $e->getResponse();
        } catch (ServerException $e) {
            $this->response = $e->getResponse();
        }
    }

    protected function assertStatusCode(int $statusCode, string $message = '')
    {
        Assert::assertEquals($statusCode, $this->response->getStatusCode(), $message);
    }

    /**
     * @Then the response should have a status code :code
     * @param string $code
     */
    public function theResponseShouldHaveStatusCode($code) {
        $currentCode = $this->response->getStatusCode();
        Assert::assertEquals($code, $currentCode);
    }

    /**
     * @Then the response should be a JSON array with the following mandatory values
     * @param TableNode $table
     */
    public function theResponseShouldBeAJsonArrayWithTheFollowingMandatoryValues(?TableNode $table = null) {
        $this->response->getBody()->seek(0);
        $realResponseArray = json_decode($this->response->getBody()->getContents(), true);

        if (is_null($table)) {
            Assert::isEmpty($realResponseArray);
            return;
        }

        $expectedValues = $table->getColumnsHash();
        foreach ($expectedValues as $value) {
            Assert::assertEqualsCanonicalizing(
                json_decode($value['value'], true),
                $realResponseArray[$value['key']]
            );
        }
    }

    /**
     * @When I send :verb to :path (:status)
     */
    public function iSendVerbToPath($verb, $path, $status)
    {
        $this->sendRequest($verb, $path);
        $this->assertStatusCode($status);
    }

    /**
     * @When I send :verb to :path using rows (:status)
     */
    public function iSendVerbToPathUsingRows($verb, $path, $status, ?TableNode $table = null)
    {
        $body = [];
        if ($table) {
            $body = $table->getRowsHash();
        }
        $this->sendRequest($verb, $path, $body);
        $this->assertStatusCode($status);
    }

}
