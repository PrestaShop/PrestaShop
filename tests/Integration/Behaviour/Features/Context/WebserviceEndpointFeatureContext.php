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

class WebserviceEndpointFeatureContext extends AbstractPrestaShopFeatureContext
{
    /**
     * @var Crawler
     */
    protected $output;
    /**
     * @var string
     */
    protected $webserviceKey = '';

    /**
     * @When /^I use the Webservice Key "(.*)"$/
     */
    public function whenUseWSKey(string $webserviceKey): void
    {
        $this->webserviceKey = $webserviceKey;
    }

    /**
     * @When /^I request the Webservice with the method (GET|POST|PUT|DELETE|HEAD) on the endpoint "(.+)"$/
     */
    public function whenRequest(string $method, string $endpoint, TableNode $rows = null): void
    {
        $postFields = '';
        if (!empty($rows instanceof TableNode)) {
            $postFields = '<prestashop xmlns:xlink="http://www.w3.org/1999/xlink"><address>';
            foreach ($rows->getHash() as $hash) {
                $postFields .= sprintf('<%s><![CDATA[%s]]></%s>',
                    $hash['key'],
                    $hash['value'],
                    $hash['key']
                );
            }
            $postFields .= '</address></prestashop>';
        }
        $this->output = $this->requestWebserviceXML($this->webserviceKey, $method, $endpoint, $postFields);
    }

    /**
     * @Then /^I check the last webservice request has these values:$/
     */
    public function assertLastRequestHasValues(TableNode $rows): void
    {
        foreach ($rows->getHash() as $hash) {
            Assert::assertEquals(
                1,
                $this->output->filter('prestashop ' . $hash['key'])->count()
            );
            Assert::assertEquals(
                $hash['value'],
                $this->output->filter('prestashop ' . $hash['key'])->getNode(0)->nodeValue
            );
        }
    }

    /**
     * @Then /^I should get a number of ([0-9]+) error([s]{0,1})$/
     */
    public function assertWebserviceErrorCount(int $numErrors): void
    {
        Assert::assertEquals($numErrors, $this->output->filter('prestashop > errors > error')->count());
    }

    /**
     * @Then /^I should get a number of ([0-9]+) item[s]{0,1} of type "(.+)"$/
     */
    public function assertWebserviceReponseValues(int $numItems, string $typeItem): void
    {
        Assert::assertEquals($numItems, $this->output->filter('prestashop ' . $typeItem)->count());
    }

    /**
     * @Then /^I should get an error with code ([0-9]+) and message "(.+)"$/
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

    protected function requestWebserviceXML(
        string $wsKey,
        string $requestMethod,
        string $url,
        string $postFields = ''
    ): Crawler {
        if ($requestMethod == 'POST' || $requestMethod == 'PUT') {
            stream_wrapper_unregister('php');
            stream_register_wrapper('php', StreamWrapperPHP::class);
        }
        $output = $this->requestWebservice('XML', $wsKey, $requestMethod, $url, $postFields);

        $crawler = new Crawler();
        $crawler->addXmlContent($output);

        return $crawler;
    }

    private function requestWebservice(
        string $output,
        string $wsKey,
        string $requestMethod,
        string $url,
        string $postFields
    ): string {
        $_SERVER['REQUEST_METHOD'] = $requestMethod;
        $_SERVER['SERVER_PROTOCOL'] = 'HTTP/1.0';

        $_GET['ws_key'] = $wsKey;
        $_GET['url'] = $url;
        $_GET['output_format'] = $output;

        if ($requestMethod == 'PUT' || $requestMethod == 'POST') {
            file_put_contents('php://input', 'xml=' . $postFields);
        }

        ob_start();
        require _PS_ROOT_DIR_ . '/webservice/dispatcher.php';

        return ob_get_clean();
    }
}

class StreamWrapperPHP
{
    /**
     * @var int
     */
    protected $index = 0;
    protected $length = null;
    protected $data = '';

    public $context;

    public function __construct()
    {
        if (file_exists($this->buffer_filename())) {
            $this->data = file_get_contents($this->buffer_filename());
        }
        $this->index = 0;
        $this->length = strlen($this->data);
    }

    protected function buffer_filename(): string
    {
        return sys_get_temp_dir() . '/php_input.txt';
    }

    public function stream_open($path, $mode, $options, &$opened_path): bool
    {
        return true;
    }

    public function stream_close()
    {
    }

    public function stream_stat(): array
    {
        return [];
    }

    public function stream_flush(): bool
    {
        return true;
    }

    public function stream_read(int $count): string
    {
        if (is_null($this->length) === true) {
            $this->length = strlen($this->data);
        }
        $length = min($count, $this->length - $this->index);
        $data = substr($this->data, $this->index);
        $this->index = $this->index + $length;

        return $data;
    }

    public function stream_eof()
    {
        return $this->index >= $this->length ? true : false;
    }

    public function stream_write($data)
    {
        return file_put_contents($this->buffer_filename(), $data);
    }

    public function unlink()
    {
        if (file_exists($this->buffer_filename())) {
            unlink($this->buffer_filename());
        }
        $this->data = '';
        $this->index = 0;
        $this->length = 0;
    }
}
