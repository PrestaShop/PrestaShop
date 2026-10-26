<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Behaviour\Features\Context;

use Behat\Gherkin\Node\TableNode;
use Order;
use PHPUnit\Framework\Assert;
use RuntimeException;
use Symfony\Component\DomCrawler\Crawler;
use Tests\Integration\Behaviour\Features\Context\Util\StreamWrapperPHP;
use WebserviceRequest;

class WebserviceEndpointFeatureContext extends AbstractPrestaShopFeatureContext
{
    /**
     * @var Crawler
     */
    protected $lastOutput;

    /**
     * @var string
     */
    protected $lastRawOutput = '';

    /**
     * @When /^I use Webservice with key "([^"]*)" to list "([^"]+)"$/
     */
    public function whenRequestList(string $webserviceKey, string $endpoint): void
    {
        $this->lastOutput = $this->whenRequest($webserviceKey, 'GET', $endpoint);
    }

    /**
     * @When /^I use Webservice with key "([^"]*)" to fast view "([^"]+)"$/
     */
    public function whenRequestHead(string $webserviceKey, string $endpoint): void
    {
        $this->whenRequest($webserviceKey, 'HEAD', $endpoint);
    }

    /**
     * @When /^I use Webservice with key "([^"]*)" to remove "([^"]+)" with reference "([^"]+)"$/
     */
    public function whenRequestDelete(string $webserviceKey, string $endpoint, string $reference): void
    {
        if ($reference === 'unknown') {
            $id = 1;
        } else {
            $id = (int) SharedStorage::getStorage()->get($reference);
        }
        $this->whenRequest($webserviceKey, 'DELETE', $endpoint . '/' . $id);
    }

    /**
     * @When /^I use Webservice with key "([^"]*)" to add "([^"]+)" for reference "([^"]+)"$/
     */
    public function whenRequestPost(string $webserviceKey, string $endpoint, string $reference, ?TableNode $rows = null): void
    {
        $output = $this->whenRequest($webserviceKey, 'POST', $endpoint, $rows ? $this->getRowsWithReferences($rows) : null);
        $this->lastOutput = $output;

        if ($output->filter('prestashop > errors > error')->count() !== 0) {
            return;
        }
        $idObject = $output->filter('prestashop id')->getNode(0)->nodeValue;
        SharedStorage::getStorage()->set($reference, $idObject);
    }

    /**
     * @When /^I use Webservice with key "([^"]*)" to update "([^"]+)" for reference "([^"]+)"$/
     */
    public function whenRequestPut(string $webserviceKey, string $endpoint, string $reference, ?TableNode $rows = null): void
    {
        if (!empty($rows)) {
            $rows = $this->getRowsWithReferences($rows);
            $rows[] = [
                'key' => 'id',
                'value' => (int) SharedStorage::getStorage()->get($reference),
            ];
        }
        $this->lastOutput = $this->whenRequest($webserviceKey, 'PUT', $endpoint, $rows);
    }

    /**
     * @When /^I use Webservice with key "(.*)" to list "(.+)" with "(id_shop)" from shop reference "(.+)"$/
     */
    public function whenRequestListWithShopReference(string $webserviceKey, string $endpoint, string $parameterName, string $shopReference): void
    {
        $shopId = (int) SharedStorage::getStorage()->get($shopReference);
        $separator = strpos($endpoint, '?') === false ? '?' : '&';
        $this->lastOutput = $this->whenRequest($webserviceKey, 'GET', sprintf('%s%s%s=%d', $endpoint, $separator, $parameterName, $shopId));
    }

    /**
     * @When /^I use Webservice with key "(.*)" to list "(.+)" with "(id_shop)" value "(.+)"$/
     */
    public function whenRequestListWithShopParameterValue(string $webserviceKey, string $endpoint, string $parameterName, string $parameterValue): void
    {
        $separator = strpos($endpoint, '?') === false ? '?' : '&';
        $this->lastOutput = $this->whenRequest($webserviceKey, 'GET', sprintf('%s%s%s=%s', $endpoint, $separator, $parameterName, $parameterValue));
    }

    /**
     * @When /^I use Webservice with key "(.*)" to list "(.+)" with "(id_shop_group|id_group_shop)" from group reference "(.+)"$/
     */
    public function whenRequestListWithGroupReference(string $webserviceKey, string $endpoint, string $parameterName, string $shopGroupReference): void
    {
        $shopGroupId = (int) SharedStorage::getStorage()->get($shopGroupReference);
        $separator = strpos($endpoint, '?') === false ? '?' : '&';
        $this->lastOutput = $this->whenRequest($webserviceKey, 'GET', sprintf('%s%s%s=%d', $endpoint, $separator, $parameterName, $shopGroupId));
    }

    /**
     * @When /^I use Webservice with key "(.*)" to view "(.+)" for reference "(.+)" with "(id_shop)" from shop reference "(.+)"$/
     */
    public function whenRequestDetailWithShopReference(string $webserviceKey, string $endpoint, string $reference, string $parameterName, string $shopReference): void
    {
        $id = (int) SharedStorage::getStorage()->get($reference);
        $shopId = (int) SharedStorage::getStorage()->get($shopReference);
        $this->lastOutput = $this->whenRequest($webserviceKey, 'GET', sprintf('%s/%d?%s=%d', $endpoint, $id, $parameterName, $shopId));
    }

    /**
     * @Given /^the order with reference "(.+)" is stored as "(.+)" and its first detail as "(.+)"$/
     */
    public function storeOrderAndFirstDetail(string $orderReference, string $reference, string $detailReference): void
    {
        $order = Order::getByReference($orderReference)->getFirst();
        if (!$order instanceof Order) {
            throw new RuntimeException(sprintf('Order with reference "%s" not found', $orderReference));
        }

        SharedStorage::getStorage()->set($reference, (int) $order->id);
        SharedStorage::getStorage()->set($detailReference, (int) $order->getOrderDetailList()[0]['id_order_detail']);
    }

    /**
     * @When /^I use Webservice with key "(.*)" to update "(.+)" for reference "(.+)" with "(id_shop)" from shop reference "(.+)"$/
     */
    public function whenRequestPutWithShopReference(string $webserviceKey, string $endpoint, string $reference, string $parameterName, string $shopReference, ?TableNode $rows = null): void
    {
        $shopId = (int) SharedStorage::getStorage()->get($shopReference);
        $endpointWithQuery = sprintf('%s?%s=%d', $endpoint, $parameterName, $shopId);
        $this->whenRequestPut($webserviceKey, $endpointWithQuery, $reference, $rows);
    }

    /**
     * @When /^I use Webservice with key "(.*)" to add "(.+)" for reference "(.+)" with "(id_shop)" from shop reference "(.+)"$/
     */
    public function whenRequestPostWithShopReference(string $webserviceKey, string $endpoint, string $reference, string $parameterName, string $shopReference, ?TableNode $rows = null): void
    {
        $shopId = (int) SharedStorage::getStorage()->get($shopReference);
        $this->whenRequestPost($webserviceKey, sprintf('%s?%s=%d', $endpoint, $parameterName, $shopId), $reference, $rows);
    }

    /**
     * @When /^I use Webservice with key "(.*)" to update "(.+)" for reference "(.+)" with "(id_shop)" value "(.+)"$/
     */
    public function whenRequestPutWithShopParameterValue(string $webserviceKey, string $endpoint, string $reference, string $parameterName, string $parameterValue, ?TableNode $rows = null): void
    {
        $this->whenRequestPut($webserviceKey, sprintf('%s?%s=%s', $endpoint, $parameterName, $parameterValue), $reference, $rows);
    }

    /**
     * @When /^I use Webservice with key "(.*)" to view "(.+)" for reference "(.+)" with "(id_shop_group|id_group_shop)" from group reference "(.+)"$/
     */
    public function whenRequestDetailWithGroupReference(string $webserviceKey, string $endpoint, string $reference, string $parameterName, string $shopGroupReference): void
    {
        $id = (int) SharedStorage::getStorage()->get($reference);
        $shopGroupId = (int) SharedStorage::getStorage()->get($shopGroupReference);
        $this->lastOutput = $this->whenRequest($webserviceKey, 'GET', sprintf('%s/%d?%s=%d', $endpoint, $id, $parameterName, $shopGroupId));
    }

    /**
     * @When /^I use Webservice with key "(.*)" to remove "(.+)" with reference "(.+)" and "(id_shop)" from shop reference "(.+)"$/
     */
    public function whenRequestDeleteWithShopReference(string $webserviceKey, string $endpoint, string $reference, string $parameterName, string $shopReference): void
    {
        $id = (int) SharedStorage::getStorage()->get($reference);
        $shopId = (int) SharedStorage::getStorage()->get($shopReference);
        $this->lastOutput = $this->whenRequest($webserviceKey, 'DELETE', sprintf('%s/%d?%s=%d', $endpoint, $id, $parameterName, $shopId));
    }

    /**
     * @Then /^using Webservice with key "(.*)" to view "(.+)" for reference "(.+)", I should get following properties\:$/
     */
    public function assertLastRequestHasValues(string $webserviceKey, string $endpoint, string $reference, TableNode $rows): void
    {
        $id = (int) SharedStorage::getStorage()->get($reference);
        $output = $this->whenRequest($webserviceKey, 'GET', $endpoint . '/' . $id);

        foreach ($rows->getHash() as $hash) {
            Assert::assertEquals(
                1,
                $output->filter('prestashop ' . $hash['key'])->count(),
                sprintf(
                    'The key %s has not been found',
                    $hash['key']
                )
            );
            Assert::assertEquals(
                $hash['value'],
                $output->filter('prestashop ' . $hash['key'])->getNode(0)->nodeValue,
                sprintf(
                    'The key %s has not the expected value %s : %s',
                    $hash['key'],
                    $hash['value'],
                    $output->filter('prestashop ' . $hash['key'])->getNode(0)->nodeValue
                )
            );
        }
    }

    /**
     * @Then /^I should get (\d+) errors?$/
     */
    public function assertWebserviceErrorCount(int $numErrors): void
    {
        Assert::assertEquals($numErrors, $this->lastOutput->filter('prestashop > errors > error')->count());
    }

    /**
     * @Then /^I should get (\d+) items? of type "(.+)"$/
     */
    public function assertWebserviceReponseValues(int $numItems, string $typeItem): void
    {
        Assert::assertEquals($numItems, $this->lastOutput->filter('prestashop ' . $typeItem)->count());
    }

    /**
     * @Then /^I should get an error with code (\d+) and message "(.+)"$/
     */
    public function assertWebserviceError(int $errorCode, string $errorMessage): void
    {
        $errors = $this->lastOutput->filter('prestashop > errors > error')->each(function ($item) {
            return [
                'code' => (int) $item->filter('code')->text(),
                'message' => $item->filter('message')->text(),
            ];
        });
        foreach ($errors as $error) {
            if ($error['code'] === $errorCode) {
                Assert::assertEquals($errorMessage, $error['message']);

                return;
            }
        }

        throw new RuntimeException(sprintf('Error with code %d was not found', $errorCode));
    }

    /**
     * @Then /^I should get an error with code (\d+)$/
     */
    public function assertWebserviceErrorCode(int $errorCode): void
    {
        $errors = $this->lastOutput->filter('prestashop > errors > error')->each(function ($item) {
            return (int) $item->filter('code')->text();
        });

        Assert::assertContains($errorCode, $errors, sprintf('Error with code %d was not found', $errorCode));
    }

    /**
     * @Then /^webservice list should (contain|not contain) the record with reference "(.+)"$/
     */
    public function assertWebserviceListContainsRecord(string $expectation, string $reference): void
    {
        $id = (int) SharedStorage::getStorage()->get($reference);
        // A full display gives the id as a child node, a minimal one as an attribute
        $listedIds = $this->lastOutput->filter('prestashop > * > *')->each(function (Crawler $item): int {
            $idNode = $item->filter('id');

            return (int) ($idNode->count() ? $idNode->text() : $item->attr('id'));
        });

        if ($expectation === 'contain') {
            Assert::assertContains($id, $listedIds, sprintf('Expected the list to contain the record %d', $id));

            return;
        }

        Assert::assertNotContains($id, $listedIds, sprintf('Expected the list to not contain the record %d', $id));
    }

    /**
     * @Then /^webservice response should (contain|not contain) text "(.+)"$/
     */
    public function assertWebserviceResponseContainsText(string $expectation, string $text): void
    {
        $contains = strpos($this->lastRawOutput, $text) !== false;

        if ($expectation === 'contain') {
            Assert::assertTrue($contains, sprintf('Expected response to contain "%s"', $text));

            return;
        }

        Assert::assertFalse($contains, sprintf('Expected response to not contain "%s"', $text));
    }

    private function whenRequest(string $webserviceKey, string $method, string $endpoint, ?array $rows = null): Crawler
    {
        $postFields = '';
        if (!empty($rows)) {
            $itemNode = $this->getItemFromEndpoint($endpoint);

            $postFields = '<prestashop xmlns:xlink="http://www.w3.org/1999/xlink"><' . $itemNode . '>';
            foreach ($rows as $hash) {
                $postFields .= sprintf('<%s><![CDATA[%s]]></%s>',
                    $hash['key'],
                    $hash['value'],
                    $hash['key']
                );
            }
            $postFields .= '</' . $itemNode . '></prestashop>';
        }

        return $this->requestWebserviceXML($webserviceKey, $method, $endpoint, $postFields);
    }

    /**
     * A row may give a "reference" column instead of a value, resolved to the id stored under that reference.
     */
    private function getRowsWithReferences(TableNode $rows): array
    {
        return array_map(static function (array $row): array {
            if (!empty($row['reference'])) {
                $row['value'] = (int) SharedStorage::getStorage()->get($row['reference']);
            }

            return ['key' => $row['key'], 'value' => $row['value']];
        }, $rows->getHash());
    }

    private function getItemFromEndpoint(string $endpoint): string
    {
        $endpoint = explode('?', $endpoint, 2)[0];
        $resources = WebserviceRequest::getResources();

        return array_key_exists($endpoint, $resources) ? strtolower($resources[$endpoint]['class']) : '';
    }

    private function requestWebserviceXML(
        string $wsKey,
        string $requestMethod,
        string $url,
        string $postFields = ''
    ): Crawler {
        $output = $this->requestWebservice('XML', $wsKey, $requestMethod, $url, $postFields);
        $this->lastRawOutput = $output;

        $crawler = new Crawler();
        $crawler->addXmlContent($output);

        return $crawler;
    }

    /**
     * Request Webservice directly with a call to the dispatcher file
     * So we need to define some $_SERVER & $_GET variables.
     *
     * @param string $output
     * @param string $wsKey
     * @param string $requestMethod
     * @param string $url
     * @param string $postFields
     *
     * @return string
     */
    private function requestWebservice(
        string $output,
        string $wsKey,
        string $requestMethod,
        string $url,
        string $postFields
    ): string {
        // Define mandatory values directly used in webservice/dispatcher.php
        $_SERVER['REQUEST_METHOD'] = $requestMethod;
        $_SERVER['SERVER_PROTOCOL'] = 'HTTP/1.0';

        // dispatcher.php reads the request parameters from $_GET, and only the resource path from $_GET['url']
        $queryParameters = [];
        if (strpos($url, '?') !== false) {
            [$url, $queryString] = explode('?', $url, 2);
            parse_str($queryString, $queryParameters);
        }
        foreach ($queryParameters as $name => $value) {
            $_GET[$name] = $value;
        }

        $_GET['ws_key'] = $wsKey;
        $_GET['url'] = $url;
        $_GET['output_format'] = $output;

        if ($requestMethod == 'PUT' || $requestMethod == 'POST') {
            StreamWrapperPHP::register();
            file_put_contents('php://input', 'xml=' . $postFields);
        }

        ob_start();
        require _PS_ROOT_DIR_ . '/webservice/dispatcher.php';
        /* @phpstan-ignore-next-line */
        if (isset($request) && $request instanceof WebserviceRequest) {
            $request::resetStaticCache();
        }

        $return = ob_get_clean();

        // Unset variables
        unset(
            $_SERVER['REQUEST_METHOD'],
            $_SERVER['SERVER_PROTOCOL'],
            $_GET['ws_key'],
            $_GET['url'],
            $_GET['output_format']
        );
        foreach (array_keys($queryParameters) as $name) {
            unset($_GET[$name]);
        }

        StreamWrapperPHP::unregister();

        return $return;
    }
}
