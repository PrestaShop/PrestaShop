<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\PrestaShopBundle\Controller\Sell\Customer\Address;

use Address;
use Country;
use Customer;
use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use Symfony\Component\DomCrawler\Crawler;
use Tests\Integration\Core\Form\IdentifiableObject\Handler\FormHandlerChecker;
use Tests\Integration\PrestaShopBundle\Controller\FormGridControllerTestCase;
use Tests\Integration\PrestaShopBundle\Controller\TestEntityDTO;
use Tests\Resources\Resetter\ConfigurationResetter;

class AddressControllerTest extends FormGridControllerTestCase
{
    private int $countryId;

    /**
     * {@inheritDoc}
     */
    public function setUp(): void
    {
        parent::setUp();

        // We get the country ID for Lithuania, and we configure this country as the default one so the controller will
        // generate a form adapted to this country (especially regarding states selector)
        $this->countryId = Country::getByIso('LT');
        $configuration = $this->client->getContainer()->get(ConfigurationInterface::class);
        $configuration->set('PS_COUNTRY_DEFAULT', $this->countryId);
    }

    /**
     * {@inheritDoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();
        ConfigurationResetter::resetConfiguration();
    }

    public function testIndex(): int
    {
        $adresses = $this->getEntitiesFromGrid();
        // @phpstan-ignore-next-line
        $this->assertNotEmpty($adresses);

        return $adresses->count();
    }

    /**
     * @depends testIndex
     *
     * @param int $initialEntityCount
     *
     * @return int
     */
    public function testCreate(int $initialEntityCount): int
    {
        $this->client->disableReboot();

        // Select the existing registered customer by ID instead of resolving the email in the form.
        $customerId = (int) Customer::customerExists('pub@prestashop.com', true);
        $this->assertGreaterThan(0, $customerId);

        // First create address
        $formData = [
            'customer_address[alias]' => 'create_alias',
            'customer_address[first_name]' => 'createfirstname',
            'customer_address[last_name]' => 'createlastname',
            'customer_address[address1]' => 'createaddress1',
            'customer_address[postcode]' => '11111',
            'customer_address[city]' => 'createcity',
            'customer_address[id_country]' => $this->countryId,
        ];
        // The selected customer fields are added to the DOM by JavaScript, which DomCrawler does not execute.
        // Fill the rendered form and provide the selected customer collection directly in the POST data.
        $form = $this->getFormFromPage($this->generateCreateUrl(), $this->getCreateSubmitButtonSelector());
        $this->formFiller->fillForm($form, $formData);
        $submittedData = $form->getPhpValues();
        $submittedData['customer_address']['selected_customer'] = [
            ['id_customer' => $customerId],
        ];

        $this->client->request($form->getMethod(), $form->getUri(), $submittedData);

        $addressId = $this->getFormHandlerChecker()->getLastCreatedId();
        $this->assertNotNull($addressId);

        // Check that the address is linked to the selected customer.
        $address = new Address($addressId);
        $this->assertSame($customerId, (int) $address->id_customer);

        // Check that there is one more address in the list
        $newAddresses = $this->getEntitiesFromGrid();
        $this->assertCount($initialEntityCount + 1, $newAddresses);
        $this->assertCollectionContainsEntity($newAddresses, $addressId);

        // The selected customer is only part of the creation form, not the edit form.
        $this->assertFormValuesFromPage(
            ['addressId' => $addressId],
            $formData
        );

        return $addressId;
    }

    /**
     * @depends testCreate
     *
     * @param int $addressId
     *
     * @return int
     */
    public function testEdit(int $addressId): int
    {
        $this->client->disableReboot();

        // First update the address with a few data
        $formData = [
            'customer_address[alias]' => 'edit_alias',
            'customer_address[first_name]' => 'editfirstname',
            'customer_address[last_name]' => 'editlastname',
            'customer_address[address1]' => 'editaddress1',
            'customer_address[postcode]' => '11111',
            'customer_address[city]' => 'editcity',
            'customer_address[id_country]' => $this->countryId,
        ];

        $this->editEntityFromPage(['addressId' => $addressId], $formData);

        // Then check that it was correctly updated
        $this->assertFormValuesFromPage(
            ['addressId' => $addressId],
            $formData
        );

        return $addressId;
    }

    /**
     * @depends testEdit
     *
     * @param int $addressId
     *
     * @return int
     */
    public function testFilters(int $addressId): int
    {
        $gridFilters = [
            ['address[id_address]' => $addressId],
            ['address[firstname]' => 'editfirstname'],
            ['address[lastname]' => 'editlastname'],
            ['address[address1]' => 'editaddress1'],
            ['address[postcode]' => '11111'],
            ['address[city]' => 'editcity'],
            ['address[id_country]' => $this->countryId],
        ];

        foreach ($gridFilters as $testFilter) {
            $addresses = $this->getFilteredEntitiesFromGrid($testFilter);
            $this->assertGreaterThanOrEqual(1, count($addresses), sprintf(
                'Expected at least one address with filters %s',
                var_export($testFilter, true)
            ));
            $this->assertCollectionContainsEntity($addresses, $addressId);
        }

        return $addressId;
    }

    /**
     * @depends testFilters
     *
     * @param int $addressId
     */
    public function testDelete(int $addressId): void
    {
        $this->client->disableReboot();

        $addresses = $this->getEntitiesFromGrid();
        $initialEntityCount = $addresses->count();

        $this->deleteEntityFromPage('admin_addresses_delete', ['addressId' => $addressId]);

        $newAddresses = $this->getEntitiesFromGrid();
        $this->assertCount($initialEntityCount - 1, $newAddresses);
    }

    /**
     * {@inheritDoc}
     */
    protected function generateGridUrl(array $routeParams = []): string
    {
        return $this->router->generate('admin_addresses_index', $routeParams);
    }

    /**
     * {@inheritDoc}
     */
    protected function getGridSelector(): string
    {
        return '#address_grid_table';
    }

    /**
     * {@inheritDoc}
     */
    protected function getFilterSearchButtonSelector(): string
    {
        return 'address[actions][search]';
    }

    /**
     * {@inheritDoc}
     */
    protected function parseEntityFromRow(Crawler $tr, int $i): TestEntityDTO
    {
        return new TestEntityDTO(
            (int) trim($tr->filter('.column-id_address')->text()),
            [
                'firstName' => trim($tr->filter('.column-lastname')->text()),
                'lastName' => trim($tr->filter('.column-address1')->text()),
                'address' => trim($tr->filter('.column-postcode')->text()),
                'postCode' => trim($tr->filter('.column-city')->text()),
                'city' => trim($tr->filter('.column-city')->text()),
                'country' => trim($tr->filter('.column-country_name')->text()),
            ]
        );
    }

    /**
     * {@inheritDoc}
     */
    protected function generateCreateUrl(): string
    {
        return $this->router->generate('admin_addresses_create');
    }

    /**
     * {@inheritDoc}
     */
    protected function getCreateSubmitButtonSelector(): string
    {
        return 'save-button';
    }

    /**
     * {@inheritDoc}
     */
    protected function getFormHandlerChecker(): FormHandlerChecker
    {
        /** @var FormHandlerChecker $checker */
        $checker = $this->client->getContainer()->get('prestashop.core.form.identifiable_object.handler.address_form_handler');

        return $checker;
    }

    /**
     * {@inheritDoc}
     */
    protected function generateEditUrl(array $routeParams): string
    {
        return $this->router->generate('admin_addresses_edit', $routeParams);
    }

    /**
     * {@inheritDoc}
     */
    protected function getEditSubmitButtonSelector(): string
    {
        return 'save-button';
    }
}
