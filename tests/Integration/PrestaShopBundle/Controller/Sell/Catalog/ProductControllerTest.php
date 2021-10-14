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

namespace Tests\Integration\PrestaShopBundle\Controller\Sell\Catalog;

use PrestaShop\PrestaShop\Core\Exception\TypeException;
use PrestaShop\PrestaShop\Core\FeatureFlag\FeatureFlagSettings;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\DomCrawler\Crawler;
use Tests\Integration\PrestaShopBundle\Controller\GridControllerTestCase;
use Tests\Integration\PrestaShopBundle\Controller\TestEntityDTO;

class ProductControllerTest extends GridControllerTestCase
{
    /**
     * @var bool
     */
    private $changedProductFeatureFlag = false;

    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);

        $this->createEntityRoute = 'admin_products_v2_create';
        $this->testEntityName = 'product';
        $this->deleteEntityRoute = 'admin_products_v2_delete';
        $this->formHandlerServiceId = 'prestashop.core.form.identifiable_object.product_form_handler';
        $this->saveButtonId = 'product_footer_save';
    }

    public function setUp(): void
    {
        $this->client = static::createClient();
        $productFeatureFlag = $this->client->getContainer()->get('doctrine.orm.entity_manager')->getRepository('PrestaShopBundle:FeatureFlag')->findOneBy(['name' => FeatureFlagSettings::FEATURE_FLAG_PRODUCT_PAGE_V2]);
        if (!$productFeatureFlag->isEnabled()) {
            $featureFlagModifier = $this->client->getContainer()->get('prestashop.core.feature_flags.modifier');
            $featureFlagModifier->updateConfiguration(
                [
                    FeatureFlagSettings::FEATURE_FLAG_PRODUCT_PAGE_V2 => true,
                ]
            );
            $this->changedProductFeatureFlag = true;
        }

        parent::setUp();
    }

    public function tearDown(): void
    {
        parent::tearDown();
        if ($this->changedProductFeatureFlag) {
            $featureFlagModifier = $this->client->getContainer()->get('prestashop.core.feature_flags.modifier');
            $featureFlagModifier->updateConfiguration(
                [
                    FeatureFlagSettings::FEATURE_FLAG_PRODUCT_PAGE_V2 => false,
                ]
            );
        }
    }

    protected function getIndexRoute(Router $router): string
    {
        /* Asserts amount of entities in the list increased by one and test entity exists */
        return $router->generate('admin_products_v2_index',
            [
                '_route' => 0,
                'product[offset]' => 0,
                'product[limit]' => 100,
            ]
        );
    }

    /**
     * Tests all provided entity filters
     * All filters are tested in one test make tests run faster
     *
     * @throws TypeException
     */
    public function testProductFilters(): void
    {
        foreach ($this->getTestFilters() as $testFilter) {
            $this->assertFiltersFindOnlyTestEntity($testFilter);
        }
    }

    /**
     * @return array
     */
    protected function getTestFilters(): array
    {
        /* @todo add rest of the tests, need for product form to be complete */
        return [
            ['product[name]' => 'stProd'],
            [
                'product[id_product][min_field]' => $this->getTestEntity()->getId(),
                'product[id_product][max_field]' => $this->getTestEntity()->getId(),
            ],
            [
                'product[quantity][min_field]' => $this->getTestEntity()->quantity,
                'product[quantity][max_field]' => $this->getTestEntity()->quantity,
            ],
            [
                'product[price_tax_excluded][min_field]' => $this->getTestEntity()->price,
                'product[price_tax_excluded][max_field]' => $this->getTestEntity()->price,
            ],
        ];
    }

    /**
     * @return TestEntityDTO
     */
    protected function getTestEntity(): TestEntityDTO
    {
        return new TestEntityDTO(
            $this->testEntityId,
            [
                'name' => 'testProductName',
                'quantity' => 987,
                'price' => '87,7',
            ]
        );
    }

    /**
     * @param $tr
     * @param $i
     *
     * @return TestEntityDTO
     */
    protected function getEntity(Crawler $tr, int $i): TestEntityDTO
    {
        return new TestEntityDTO(
            (int) trim($tr->filter('.column-id_product')->text()),
           [
           ]
        );
    }

    /**
     * Gets modifications that are needed to fill address form
     *
     * @return array
     */
    protected function getCreateEntityFormModifications(): array
    {
        /** @todo add rest of the tests, need for product form to be complete */
        $testEntity = $this->getTestEntity();

        return [
            'product[header][name][1]' => $testEntity->name,
            // 'product[shortcuts][stock][quantity]' => $testEntity->quantity,
            'product[stock][quantities][quantity][delta]' => $testEntity->quantity,
            'product[shipping][additional_shipping_cost]' => 0,
            'product[pricing][retail_price][price_tax_excluded]' => $testEntity->price,
            // 'product[shortcuts][retail_price][price_tax_excluded]' => $testEntity->price,
            // 'product[shortcuts][retail_price][price_tax_included]' => $testEntity->price,
        ];
    }
}
