<?php

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
    /**
     * @var bool
     */
    private $handlePartialUpdate;

    /**
     * @var bool
     */
    private $handleStrictPartialUpdate;

    protected function setUp()
    {
        $this->handlePartialUpdate = true;
        $this->handleStrictPartialUpdate = false;
    }

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
        $this->assertHandlerData(['basic' => ['name' => [1 => 'Test Product']]], $createData);

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

        $productForm = $this->fillProductForm($crawler, $formModifications);
        $client->submit($productForm);

        // If update happens correctly then we are redirect to the same edition page
        $redirectionProductId = $this->assertSuccessfulRedirection($client);
        $this->assertEquals($productId, $redirectionProductId);

        // Now we check that the correct data were use by the form handler
        $dataChecker = $client->getContainer()->get('test.integration.core.form.identifiable_object.data_handler.product_form_data_handler_checker');
        $updateData = $dataChecker->getLastUpdateData();
        $this->assertHandlerData($expectedUpdateData, $updateData);
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

        yield [
            [
                'product[shipping][carriers]' => [1, 3],
            ],
            [
                'shipping' => [
                    'carriers' => [1, 3],
                ],
            ],
        ];
    }

    /**
     * @param array $expectedData
     * @param array $handlerData
     */
    private function assertHandlerData(array $expectedData, array $handlerData): void
    {
        if ($this->handlePartialUpdate && !$this->handleStrictPartialUpdate) {
            // This method is deprecated in PHPUnit for PHP 8.0 but we can't use more recent libraries that replace this
            // because they require more recent version of PHP than ours, so for now we keep using this one
            $this->assertArraySubset($expectedData, $handlerData);
        } else {
            $this->assertEquals($expectedData, $handlerData);
        }
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
}
