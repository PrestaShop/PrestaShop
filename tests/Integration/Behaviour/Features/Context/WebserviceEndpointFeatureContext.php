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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
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
    protected $output;

    /**
     * @When /^I use Webservice with key "(.*)" for listing "(.+)"$/
     */
    public function whenRequestList(string $webserviceKey, string $endpoint): void
    {
        $this->whenRequest($webserviceKey, 'GET', $endpoint);
    }

    /**
     * @When /^I use Webservice with key "(.*)" for fast viewing "(.+)"$/
     */
    public function whenRequestHead(string $webserviceKey, string $endpoint): void
    {
        $this->whenRequest($webserviceKey, 'HEAD', $endpoint);
    }

    /**
     * @When /^I use Webservice with key "(.*)" for removing "(.+)" with id ([0-9]+)$/
     */
    public function whenRequestDelete(string $webserviceKey, string $endpoint, int $id): void
    {
        $this->whenRequest($webserviceKey, 'DELETE', $endpoint . '/' . $id);
    }

    /**
     * @When /^I use Webservice with key "(.*)" for adding "(.+)"$/
     */
    public function whenRequestPost(string $webserviceKey, string $endpoint, TableNode $rows = null): void
    {
        $this->whenRequest($webserviceKey, 'POST', $endpoint, $rows);
    }

    /**
     * @When /^I use Webservice with key "(.*)" for updating "(.+)"$/
     */
    public function whenRequestPut(string $webserviceKey, string $endpoint, TableNode $rows = null): void
    {
        $this->whenRequest($webserviceKey, 'PUT', $endpoint, $rows);
    }

    /**
     * @Then /^using Webservice with key "(.*)" for viewing "(.+)" with id ([0-9]+), I should get following properties\:$/
     */
    public function assertLastRequestHasValues(string $webserviceKey, string $endpoint, int $id, TableNode $rows): void
    {
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
     * @Then /^I should get a number of (\d+) errors?$/
     */
    public function assertWebserviceErrorCount(int $numErrors): void
    {
        Assert::assertEquals($numErrors, $this->output->filter('prestashop > errors > error')->count());
    }

    /**
     * @Then /^I should get a number of (\d+) items? of type "(.+)"$/
     */
    public function assertWebserviceReponseValues(int $numItems, string $typeItem): void
    {
        Assert::assertEquals($numItems, $this->output->filter('prestashop ' . $typeItem)->count());
    }

    /**
     * @Then /^I should get an error with code (\d+) and message "(.+)"$/
     */
    public function assertWebserviceError(int $errorCode, string $errorMessage): void
    {
        $errors = $this->output->filter('prestashop > errors > error')->each(function ($item) {
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

    private function whenRequest(string $webserviceKey, string $method, string $endpoint, TableNode $rows = null): Crawler
    {
        $postFields = '';
        if (!empty($rows)) {
            $itemNode = $this->getItemFromEndpoint($endpoint);

            $postFields = '<prestashop xmlns:xlink="http://www.w3.org/1999/xlink"><' . $itemNode . '>';
            foreach ($rows->getHash() as $hash) {
                $postFields .= sprintf('<%s><![CDATA[%s]]></%s>',
                    $hash['key'],
                    $hash['value'],
                    $hash['key']
                );
            }
            $postFields .= '</' . $itemNode . '></prestashop>';
        }
        $this->output = $this->requestWebserviceXML($webserviceKey, $method, $endpoint, $postFields);

        return $this->output;
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
            stream_wrapper_unregister('php');
            stream_register_wrapper('php', StreamWrapperPHP::class);
            file_put_contents('php://input', 'xml=' . $postFields);
        }

        ob_start();
        require _PS_ROOT_DIR_ . '/webservice/dispatcher.php';
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

        return $return;
    }
}
