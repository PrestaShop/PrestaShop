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

use Cache;
use DOMElement;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductType;
use PrestaShop\PrestaShop\Core\FeatureFlag\FeatureFlagSettings;
use PrestaShopBundle\Entity\Repository\FeatureFlagRepository;
use RuntimeException;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\DomCrawler\Form;
use Tests\Integration\Core\Form\IdentifiableObject\Handler\FormHandlerChecker;
use Tests\Integration\PrestaShopBundle\Controller\FormGridControllerTestCase;
use Tests\Integration\PrestaShopBundle\Controller\TestEntityDTO;
use Tests\Resources\Resetter\ProductResetter;

class CombinationControllerTest extends FormGridControllerTestCase
{
    /**
     * @var bool
     */
    private $changedProductFeatureFlag = false;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        static::mockContext();
        ProductResetter::resetProducts();
    }

    public static function tearDownAfterClass(): void
    {
        parent::tearDownAfterClass();
        ProductResetter::resetProducts();
    }

    public function setUp(): void
    {
        parent::setUp();
        $featureFlagRepository = $this->client->getContainer()->get(FeatureFlagRepository::class);
        if (!$featureFlagRepository->isEnabled(FeatureFlagSettings::FEATURE_FLAG_PRODUCT_PAGE_V2)) {
            $featureFlagRepository->enable(FeatureFlagSettings::FEATURE_FLAG_PRODUCT_PAGE_V2);
            $this->changedProductFeatureFlag = true;
        }
    }

    public function tearDown(): void
    {
        if ($this->changedProductFeatureFlag) {
            $featureFlagRepository = $this->client->getContainer()->get(FeatureFlagRepository::class);
            $featureFlagRepository->disable(FeatureFlagSettings::FEATURE_FLAG_PRODUCT_PAGE_V2);
        }

        // Call parent tear down later or the kernel will be shut down
        parent::tearDown();
    }

    /**
     * @return int
     */
    public function testCreate(): int
    {
        // First create product, we don't check if this works very thoroughly as it is already handled
        // by ProductControllerTest this is only to have a parent for combinations.
        $formData = [
            'create_product[type]' => ProductType::TYPE_COMBINATIONS,
        ];

        $createEntityUrl = $this->router->generate('admin_products_create');

        $this->fillAndSubmitEntityForm($createEntityUrl, $formData, 'create_product_create');
        $formHandlerChecker = $this->client->getContainer()->get('prestashop.core.form.identifiable_object.product_form_handler');

        $createdProductId = $formHandlerChecker->getLastCreatedId();
        self::assertNotNull($createdProductId);

        return $createdProductId;
    }

    /**
     * @depends testCreate
     *
     * @param int $productId
     *
     * @return int[]
     */
    public function testGenerateCombinations(int $productId): array
    {
        $this->client->xmlHttpRequest('GET', $this->router->generate('admin_all_attribute_groups'));
        $attributeGroups = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertNotFalse($attributeGroups);
        $this->assertNotEmpty($attributeGroups);
        $this->assertEquals('Size', $attributeGroups[0]['name']);
        $this->assertNotEmpty($attributeGroups[0]['attributes']);
        $sizeAttributes = [];
        foreach ($attributeGroups[0]['attributes'] as $attribute) {
            $sizeAttributes[] = $attribute['id'];
        }
        $requestParameters = [
            'attributes' => [
                $attributeGroups[0]['id'] => $sizeAttributes,
            ],
        ];

        $generateCombinationsUrl = $this->router->generate('admin_products_combinations_generate', ['productId' => $productId]);
        $this->client->xmlHttpRequest('POST', $generateCombinationsUrl, $requestParameters);
        $generatedCombinations = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertNotFalse($generatedCombinations);
        $this->assertNotEmpty($generatedCombinations['combination_ids']);
        $this->assertEquals(count($sizeAttributes), count($generatedCombinations['combination_ids']));

        $generatedCombinations['product_id'] = $productId;

        return $generatedCombinations;
    }

    /**
     * @depends testGenerateCombinations
     *
     * @param array $generatedCombinations
     *
     * @return array
     */
    public function testEditDefaultCombination(array $generatedCombinations): array
    {
        $defaultCombinationId = $generatedCombinations['combination_ids'][0];
        // First assert that first combination is the default one
        $formData = [
            'combination_form[header][is_default]' => true,
        ];
        $this->assertCombinationForm($defaultCombinationId, $formData);

        $updatedFormData = [
            'combination_form[stock][quantities][delta_quantity][delta]' => 42,
            'combination_form[stock][quantities][minimal_quantity]' => 13,
            'combination_form[stock][options][stock_location]' => 'warehouse13',
            'combination_form[stock][options][disabling_switch_low_stock_threshold]' => 1,
            'combination_form[stock][options][low_stock_threshold]' => 9,
            'combination_form[stock][available_date]' => '2042-11-15',
            'combination_form[stock][available_now_label][1]' => 'available now',
            'combination_form[stock][available_later_label][1]' => 'available later',
            'combination_form[price_impact][price_tax_excluded]' => 42.00,
            'combination_form[price_impact][unit_price_tax_excluded]' => 51.00,
            'combination_form[price_impact][weight]' => '12.34',
            'combination_form[price_impact][wholesale_price]' => 1.34,
            'combination_form[references][reference]' => 'reference',
            'combination_form[references][mpn]' => 'mpn',
            'combination_form[references][upc]' => '1234',
            'combination_form[references][ean_13]' => '12345',
            'combination_form[references][isbn]' => 'ISBN 978-0-596-52068-7',
        ];
        $this->editCombinationForm($defaultCombinationId, $updatedFormData);

        $expectedFormData = [
            'combination_form[stock][quantities][delta_quantity][quantity]' => 42,
            'combination_form[stock][quantities][delta_quantity][delta]' => 0,
            'combination_form[stock][quantities][minimal_quantity]' => 13,
            'combination_form[stock][options][stock_location]' => 'warehouse13',
            'combination_form[stock][options][disabling_switch_low_stock_threshold]' => true,
            'combination_form[stock][options][low_stock_threshold]' => 9,
            'combination_form[stock][available_date]' => '2042-11-15',
            'combination_form[stock][available_now_label][1]' => 'available now',
            'combination_form[stock][available_later_label][1]' => 'available later',
            'combination_form[price_impact][price_tax_excluded]' => 42.00,
            'combination_form[price_impact][unit_price_tax_excluded]' => 51.00,
            'combination_form[price_impact][weight]' => '12.34',
            'combination_form[price_impact][wholesale_price]' => 1.34,
            'combination_form[references][reference]' => 'reference',
            'combination_form[references][mpn]' => 'mpn',
            'combination_form[references][upc]' => '1234',
            'combination_form[references][ean_13]' => '12345',
            'combination_form[references][isbn]' => 'ISBN 978-0-596-52068-7',
        ];
        $this->assertCombinationForm($defaultCombinationId, $expectedFormData);

        return $generatedCombinations;
    }

    /**
     * @depends testEditDefaultCombination
     *
     * @param array $generatedCombinations
     *
     * @return array
     */
    public function testEditNotDefaultCombination(array $generatedCombinations): array
    {
        $initialDefaultCombinationId = $generatedCombinations['combination_ids'][0];
        $newDefaultCombinationId = $generatedCombinations['combination_ids'][1];

        $this->assertCombinationForm($initialDefaultCombinationId, ['combination_form[header][is_default]' => true]);
        $this->assertCombinationForm($newDefaultCombinationId, ['combination_form[header][is_default]' => false]);

        $this->editCombinationForm($newDefaultCombinationId, ['combination_form[header][is_default]' => true]);

        $this->assertCombinationForm($initialDefaultCombinationId, ['combination_form[header][is_default]' => false]);
        $this->assertCombinationForm($newDefaultCombinationId, ['combination_form[header][is_default]' => true]);

        return $generatedCombinations;
    }

    /**
     * @depends testEditNotDefaultCombination
     *
     * @param array $generatedCombinations
     *
     * @return array
     */
    public function testEditFromList(array $generatedCombinations): array
    {
        $newDefaultCombinationId = $generatedCombinations['combination_ids'][1];
        $productId = $generatedCombinations['product_id'];

        $initialData = [
            'combination_form[stock][quantities][delta_quantity][quantity]' => 0,
            'combination_form[price_impact][price_tax_excluded]' => 0.00,
            'combination_form[references][reference]' => '',
            'combination_form[header][is_default]' => true,
        ];
        $this->assertCombinationForm($newDefaultCombinationId, $initialData);

        $updateData = [
            'combination_list' => [
                [
                    'combination_id' => $newDefaultCombinationId,
                    'delta_quantity' => [
                        'delta' => 51,
                    ],
                    'impact_on_price_te' => 12.00,
                    'reference' => 'reference2',
                    'is_default' => true,
                ],
            ],
        ];
        $this->updateCombinationFromList($productId, $updateData);

        $initialData = [
            'combination_form[stock][quantities][delta_quantity][quantity]' => 51,
            'combination_form[price_impact][price_tax_excluded]' => 12.00,
            'combination_form[references][reference]' => 'reference2',
            'combination_form[header][is_default]' => true,
        ];
        $this->assertCombinationForm($newDefaultCombinationId, $initialData);

        return $generatedCombinations;
    }

    /**
     * @depends testEditFromList
     *
     * @param array $generatedCombinations
     *
     * @return array
     */
    public function testDefaultFromList(array $generatedCombinations): array
    {
        $initialDefaultCombinationId = $generatedCombinations['combination_ids'][0];
        $newDefaultCombinationId = $generatedCombinations['combination_ids'][1];
        $productId = $generatedCombinations['product_id'];

        $this->updateCombinationFromList($productId, [
            'combination_list' => [
                [
                    'combination_id' => $initialDefaultCombinationId,
                    'is_default' => true,
                ],
            ],
        ]);

        $this->assertCombinationForm($initialDefaultCombinationId, ['combination_form[header][is_default]' => true]);
        $this->assertCombinationForm($newDefaultCombinationId, ['combination_form[header][is_default]' => false]);

        return $generatedCombinations;
    }

    private function assertCombinationForm(int $combinationId, array $formData): void
    {
        $combinationForm = $this->getCombinationForm($combinationId);
        $this->formChecker->checkForm($combinationForm, $formData);
    }

    private function editCombinationForm(int $combinationId, array $formData): void
    {
        $filledEntityForm = $this->formFiller->fillForm(
            $this->getCombinationForm($combinationId),
            $formData
        );

        $this->client->submit($filledEntityForm);
        // Don't know why exactly but sometimes Combination data ere not up to date, some cache on the EntityManager maybe
        Cache::clear();
    }

    /**
     * Combination form is specific because it has no submit button, so we fetch it differently from other forms.
     *
     * @param int $combinationId
     *
     * @return Form
     */
    private function getCombinationForm(int $combinationId): Form
    {
        $crawler = $this->client->request('GET', $this->router->generate('admin_products_combinations_edit_combination', [
            'combinationId' => $combinationId,
        ]));
        $this->assertResponseIsSuccessful();
        $formCrawler = $crawler->filter('form[name="combination_form"]');

        return $formCrawler->form();
    }

    /**
     * Combination form is specific because it has no submit button, so we fetch it differently from other forms.
     *
     * @param int $productId
     * @param array $formData
     */
    private function updateCombinationFromList(int $productId, array $formData): void
    {
        // Get token from product form page
        $productCrawler = $this->client->request('GET', $this->router->generate('admin_products_edit', ['productId' => $productId]));
        $tokenCrawler = $productCrawler->filter('[name="combination_list[_token]"]');

        $tokenInput = $tokenCrawler->getNode(0);
        if (!$tokenInput instanceof DOMElement) {
            throw new RuntimeException('Could not find combination list token in product page.');
        }
        $this->assertTrue($tokenInput->hasAttribute('value'));
        $token = $tokenInput->getAttribute('value');
        $formData['combination_list']['_token'] = $token;

        $url = $this->router->generate('admin_products_combinations_update_combination_from_listing', [
            'productId' => $productId,
        ]);
        $this->client->xmlHttpRequest('PATCH', $url, $formData);
        $this->assertResponseIsSuccessful();
        $response = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertNotEmpty($response['message']);

        // Don't know why exactly but sometimes Combination data ere not up to date, some cache on the EntityManager maybe
        Cache::clear();
    }

    protected function getFormHandlerChecker(): FormHandlerChecker
    {
        /** @var FormHandlerChecker $checker */
        $checker = $this->client->getContainer()->get('prestashop.core.form.identifiable_object.combination_form_handler');

        return $checker;
    }

    protected function generateEditUrl(array $routeParams): string
    {
        return $this->router->generate('admin_products_combinations_edit_combination', $routeParams);
    }

    protected function getEditSubmitButtonSelector(): string
    {
        throw new RuntimeException('No edit submit button form for combination');
    }

    protected function generateCreateUrl(): string
    {
        throw new RuntimeException('No creation form for combination');
    }

    protected function getCreateSubmitButtonSelector(): string
    {
        throw new RuntimeException('No creation form for combination');
    }

    protected function getFilterSearchButtonSelector(): string
    {
        throw new RuntimeException('No grid for combination');
    }

    protected function generateGridUrl(array $routeParams = []): string
    {
        throw new RuntimeException('No grid for combination');
    }

    protected function getGridSelector(): string
    {
        throw new RuntimeException('No grid for combination');
    }

    protected function parseEntityFromRow(Crawler $tr, int $i): TestEntityDTO
    {
        throw new RuntimeException('No grid for combination');
    }
}
