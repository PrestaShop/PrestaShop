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

namespace Tests\Unit\PrestaShopBundle\Service\DataProvider\Admin\Marketplace;

use AppKernel;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use PrestaShopBundle\Service\DataProvider\Marketplace\ApiClient;

class ApiClientTest extends TestCase
{
    /**
     * @var ApiClient
     */
    private $apiClient;

    public function setUp(): void
    {
        parent::setUp();

        $client = new Client(['handler' => HandlerStack::create(new MockHandler([
            new Response(200),
            new Response(200),
        ]))]);

        $this->apiClient = new ApiClient(
            $client,
            'en',
            'en',
            null,
            '',
            AppKernel::VERSION
        );
    }

    public function testModuleChannel(): void
    {
        self::assertEquals($this->apiClient->getQueryParameters(), $this->getDefaultQueryParameters());
        $this->apiClient->setModuleChannel('moduleChannel');
        self::assertEquals(
            $this->apiClient->getQueryParameters(),
            array_merge(
                $this->getDefaultQueryParameters(),
                [
                    'channel' => 'moduleChannel',
                ]
            )
        );
    }

    private function getDefaultQueryParameters(): array
    {
        return [
            'format' => 'json',
            'iso_lang' => 'en',
            'iso_code' => 'en',
            'version' => AppKernel::VERSION,
            'shop_url' => '',
        ];
    }
}
