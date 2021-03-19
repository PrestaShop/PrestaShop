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

namespace Tests\Integration\Webservice;

use Configuration;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DomCrawler\Crawler;
use WebserviceKey;

class AbstractWebserviceTest extends TestCase
{
    /**
     * @var string
     */
    protected const WS_KEY = 'ABCDEFGHIJKLMNOPQRSTUVWXYZABCDEF';
    /**
     * @var WebserviceKey|null
     */
    protected $wsKey;

    protected function setUp(): void
    {
        parent::setUp();

        if (!$this->wsKey) {
            // Enable WebService
            Configuration::set('PS_WEBSERVICE', true);

            // Create WebService Key
            if (!WebserviceKey::keyExists(self::WS_KEY)) {
                $wsKey = new WebserviceKey();
                $wsKey->key = self::WS_KEY;
                $wsKey->active = true;
                $wsKey->description = '';
                $wsKey->add();
            }

            $this->wsKey = new WebserviceKey(WebserviceKey::getIdFromKey(self::WS_KEY));

            stream_wrapper_unregister('php');
            stream_register_wrapper('php', StreamWrapperPHP::class);
        }

        $this->wsKey->active = true;
        $this->wsKey->save();
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        Configuration::set('PS_WEBSERVICE', false);

        stream_wrapper_restore('php');
    }

    protected function requestWebserviceXML(
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
