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

namespace Tests\Integration\PrestaShopBundle\Controller\Admin\Sell\Catalog\Product;

use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\CommandBuilder\Product\ProductCommandsBuilder;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataHandler\ProductFormDataHandler;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\DomCrawler\Field\ChoiceFormField;
use Symfony\Component\DomCrawler\Field\FormField;
use Symfony\Component\DomCrawler\Form;

/**
 * This integration test is mostly used to ensure the partial update mechanism is correctly handled, the purpose is to check
 * that with a set number of differences only partial data is provided to the ProductFormDataHandler so that it can then create
 * only required CQRS commands.
 *
 * We don't check which commands are created it is the responsibility of the ProductFormDataHandler, we only check that the controller
 * handles the request correctly so that only partial data is provided to the handler.
 *
 * For more details about CQRS commands partial generation:
 *
 * @see ProductFormDataHandler
 * @see ProductCommandsBuilder
 *
 * To run this test run this command from project root:
 * php -d date.timezone=UTC ./vendor/bin/phpunit -c tests/Integration/phpunit.xml tests/Integration/PrestaShopBundle/Controller/Admin/Sell/Catalog/Product/ProductControllerTest.php
 */
class ProductControllerTest extends WebTestCase
{
    public function testCreateProduct(): int
    {
        $client = static::createClient();
        $router = $client->getKernel()->getContainer()->get('router');
        $createUrl = $router->generate('admin_products_v2_create');
        $crawler = $client->request('GET', $createUrl);

        // Only product name in default language should be required on creation
        $productForm = $this->fillProductForm($crawler, ['product[basic][name][1]' => 'Test Product']);
        $client->submit($productForm);

        // If creation happens correctly then we are redirect to the edition page
        $createdProductId = $this->assertSuccessfulRedirection($client);

        // Now we check that the correct data were use by the form handler
        $dataChecker = $client->getContainer()->get('test.integration.core.form.identifiable_object.data_handler.product_form_data_handler_checker');
        $createData = $dataChecker->getLastCreateData();

        // We only check a little part of the data even though it should be full
        $this->assertArraySubset(['basic' => ['name' => [1 => 'Test Product']]], $createData);

        return $createdProductId;
    }

    /**
     * @param array $formModifications
     * @param array $expectedUpdateData
     * @param int $productId
     *
     * @dataProvider getProductEditionModifications
     * @depends testCreateProduct
     */
    public function testEditProduct(array $formModifications, array $expectedUpdateData, int $productId)
    {
        $client = static::createClient();
        $router = $client->getKernel()->getContainer()->get('router');
        $createUrl = $router->generate('admin_products_v2_edit', ['productId' => $productId]);
        $crawler = $client->request('GET', $createUrl);

        $productForm = $this->fillPartialProductForm($crawler, $formModifications);
        $client->submit($productForm);

        // If update happens correctly then we are redirect to the same edition page
        $redirectionProductId = $this->assertSuccessfulRedirection($client);
        $this->assertEquals($productId, $redirectionProductId);

        // Now we check that the correct data were use by the form handler
        $dataChecker = $client->getContainer()->get('test.integration.core.form.identifiable_object.data_handler.product_form_data_handler_checker');
        $updateData = $dataChecker->getLastUpdateData();
        $this->assertEquals($expectedUpdateData, $updateData);

        // Check that the form contains the expected content
        foreach ($formModifications as $fieldName => $expectedValue) {
            $formField = $productForm[$fieldName];
            if (is_array($formField)) {
                $formValue = [];
                foreach ($formField as $subFormField) {
                    if (null !== $subFormField->getValue()) {
                        $formValue[] = $subFormField->getValue();
                    }
                }
            } else {
                $formValue = $formField->getValue();
            }
            $this->assertEquals($expectedValue, $formValue);
        }
    }

    public function getProductEditionModifications()
    {
        yield [
            // Form fields that need to be updated
            [
                'product[basic][name][1]' => 'Test Update Product',
            ],
            // Expected data in the handler
            [
                'basic' => [
                    'name' => [
                        1 => 'Test Update Product',
                    ],
                ],
            ],
        ];

        yield [
            [
                'product[shipping][carriers]' => [1, 2],
            ],
            [
                'shipping' => [
                    'carriers' => [1, 2],
                ],
            ],
        ];

        // One new is selected, one is unselected In the end we only want the two that were selected
        // and NOT the previous one
        yield [
            [
                'product[shipping][carriers]' => [1, 3],
            ],
            [
                'shipping' => [
                    // Weird indexing right? Coming from the input names
                    'carriers' => [0 => 1, 2 => 3],
                ],
            ],
        ];
    }

    /**
     * Check that request succeeded and therefore redirects to edition page
     *
     * @param Client $client
     *
     * @return int Returns the product ID from the redirection URL
     */
    private function assertSuccessfulRedirection(Client $client): int
    {
        $response = $client->getResponse();
        $this->assertTrue($response->isRedirection());
        $redirectUrl = $response->headers->get('Location');
        $parsedUrl = parse_url($redirectUrl);

        $router = $client->getContainer()->get('router');
        $routerMatching = $router->match($parsedUrl['path']);
        $this->assertArrayHasKey('_route', $routerMatching);
        $this->assertEquals('admin_products_v2_edit', $routerMatching['_route']);
        $this->assertArrayHasKey('productId', $routerMatching);
        $this->assertGreaterThan(0, $routerMatching['productId']);

        return (int) $routerMatching['productId'];
    }

    /**
     * @param Crawler $crawler
     * @param array $formModifications
     *
     * @return Form
     */
    private function fillProductForm(Crawler $crawler, array $formModifications): Form
    {
        // Get button by id #product_save
        $submitButton = $crawler->selectButton('product_save');
        $productForm = $submitButton->form();
        foreach ($formModifications as $fieldName => $formValue) {
            if (is_array($formValue)) {
                // For multi select checkboxes or select inputs
                /** @var ChoiceFormField[]|ChoiceFormField $formFields */
                $formFields = $productForm->get($fieldName);
                // Multiple checkboxes are returned as array
                if (is_array($formFields)) {
                    foreach ($formFields as $formField) {
                        if ('checkbox' === $formField->getType()) {
                            $optionValue = $formField->availableOptionValues()[0];
                            if (in_array($optionValue, $formValue)) {
                                $formField->tick();
                            } else {
                                $formField->untick();
                            }
                        } else {
                            $formField->select($formValue);
                        }
                    }
                } else {
                    $formFields->select($formValue);
                }
            } else {
                /** @var FormField $formField */
                $formField = $productForm->get($fieldName);
                $formField->setValue($formValue);
            }
        }

        return $productForm;
    }

    /**
     * @param Crawler $crawler
     * @param array $formModifications
     *
     * @return Form
     */
    private function fillPartialProductForm(Crawler $crawler, array $formModifications): Form
    {
        $productForm = $this->fillProductForm($crawler, $formModifications);

        // Remove unnecessary form fields
        $formFields = $productForm->all();
        foreach ($formFields as $formField) {
            if (in_array($formField->getName(), ['_method', 'product[_token]'])) {
                continue;
            }

            if (!array_key_exists($formField->getName(), $formModifications)) {
                $productForm->remove($formField->getName());
            }
        }

        return $productForm;
    }
}
