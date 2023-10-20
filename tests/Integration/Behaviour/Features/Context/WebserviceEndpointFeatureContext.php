<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace Tests\Integration\Behaviour\Features\Context;

use Behat\Gherkin\Node\TableNode;
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
     * @When /^I use Webservice with key "(.*)" to list "(.+)"$/
     */
    public function whenRequestList(string $webserviceKey, string $endpoint): void
    {
        $this->lastOutput = $this->whenRequest($webserviceKey, 'GET', $endpoint);
    }

    /**
     * @When /^I use Webservice with key "(.*)" to fast view "(.+)"$/
     */
    public function whenRequestHead(string $webserviceKey, string $endpoint): void
    {
        $this->whenRequest($webserviceKey, 'HEAD', $endpoint);
    }

    /**
     * @When /^I use Webservice with key "(.*)" to remove "(.+)" with reference "(.+)"$/
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
     * @When /^I use Webservice with key "(.*)" to add "(.+)" for reference "(.+)"$/
     */
    public function whenRequestPost(string $webserviceKey, string $endpoint, string $reference, TableNode $rows = null): void
    {
        $output = $this->whenRequest($webserviceKey, 'POST', $endpoint, $rows ? $rows->getHash() : null);

        if ($output->filter('prestashop > errors > error')->count() !== 0) {
            return;
        }
        $idObject = $output->filter('prestashop id')->getNode(0)->nodeValue;
        SharedStorage::getStorage()->set($reference, $idObject);
    }

    /**
     * @When /^I use Webservice with key "(.*)" to update "(.+)" for reference "(.+)"$/
     */
    public function whenRequestPut(string $webserviceKey, string $endpoint, string $reference, TableNode $rows = null): void
    {
        if (!empty($rows)) {
            $rows = $rows->getHash();
            $rows[] = [
                'key' => 'id',
                'value' => (int) SharedStorage::getStorage()->get($reference),
            ];
        }
        $this->whenRequest($webserviceKey, 'PUT', $endpoint, $rows);
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

    private function whenRequest(string $webserviceKey, string $method, string $endpoint, array $rows = null): Crawler
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

    private function getItemFromEndpoint(string $endpoint): string
    {
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

        StreamWrapperPHP::unregister();

        return $return;
    }
}
