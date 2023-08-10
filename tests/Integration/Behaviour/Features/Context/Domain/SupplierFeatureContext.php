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

namespace Tests\Integration\Behaviour\Features\Context\Domain;

use Behat\Gherkin\Node\TableNode;
use Configuration;
use Country;
use PHPUnit\Framework\Assert;
use PrestaShop\PrestaShop\Core\Domain\Supplier\Command\AddSupplierCommand;
use PrestaShop\PrestaShop\Core\Domain\Supplier\Command\DeleteSupplierCommand;
use PrestaShop\PrestaShop\Core\Domain\Supplier\Command\DeleteSupplierLogoImageCommand;
use PrestaShop\PrestaShop\Core\Domain\Supplier\Command\EditSupplierCommand;
use PrestaShop\PrestaShop\Core\Domain\Supplier\Command\ToggleSupplierStatusCommand;
use PrestaShop\PrestaShop\Core\Domain\Supplier\Exception\SupplierException;
use PrestaShop\PrestaShop\Core\Domain\Supplier\Query\GetSupplierForEditing;
use PrestaShop\PrestaShop\Core\Domain\Supplier\Query\GetSupplierForViewing;
use PrestaShop\PrestaShop\Core\Domain\Supplier\QueryResult\EditableSupplier;
use PrestaShop\PrestaShop\Core\Domain\Supplier\QueryResult\ViewableSupplier;
use PrestaShop\PrestaShop\Core\Domain\Supplier\ValueObject\SupplierId;
use RuntimeException;
use State;
use Supplier;
use Tests\Integration\Behaviour\Features\Context\Util\PrimitiveUtils;

class SupplierFeatureContext extends AbstractDomainFeatureContext
{
    /**
     * @Then /^supplier "(.+)" should have following details for product "(.+)":$/
     *
     * @param string $supplierReference
     * @param string $productName
     * @param TableNode $expectedDataTable
     */
    public function assertViewableSupplierProduct(string $supplierReference, string $productName, TableNode $expectedDataTable): void
    {
        $viewableSupplier = $this->getSupplierForViewing($supplierReference);
        $product = null;

        foreach ($viewableSupplier->getSupplierProducts() as $supplierProduct) {
            if ($supplierProduct['name'] === $productName) {
                $product = $supplierProduct;
                break;
            }
        }

        if (!$product) {
            throw new RuntimeException(sprintf('Product by name "%s" not found in viewable supplier', $productName));
        }

        $this->assertProductFromViewableSupplier($product, $expectedDataTable);
    }

    /**
     * @Given /^supplier "(.+)" should have (\d+) products associated$/
     *
     * @param string $reference
     * @param int $productsCount
     */
    public function assertSupplierProductsCount(string $reference, int $productsCount): void
    {
        $viewableSupplier = $this->getSupplierForViewing($reference);
        $products = $viewableSupplier->getSupplierProducts();

        Assert::assertEquals($productsCount, count($products), 'Unexpected supplier products count');
    }

    /**
     * @When I add new supplier :supplierReference with the following properties:
     *
     * @param string $supplierReference
     * @param TableNode $table
     */
    public function createSupplier(string $supplierReference, TableNode $table)
    {
        $data = $this->localizeByRows($table);

        try {
            /** @var SupplierId $supplierId */
            $supplierId = $this->getCommandBus()->handle(new AddSupplierCommand(
                $data['name'],
                $data['address'],
                $data['city'],
                $this->getCountryIdByName($data['country']),
                PrimitiveUtils::castStringBooleanIntoBoolean($data['enabled']),
                $data['description'],
                $data['meta title'],
                $data['meta description'],
                [],
                $this->getShopIdsByReferences($data['shops']),
                $data['address2'] ?? null,
                $data['post code'] ?? null,
                isset($data['state']) ? (int) State::getIdByName($data['state']) : null,
                $data['phone'] ?? null,
                $data['mobile phone'] ?? null,
                $data['dni'] ?? null
            ));
            $this->getSharedStorage()->set($supplierReference, $supplierId->getValue());
        } catch (SupplierException $e) {
            $this->setLastException($e);
        }
    }

    /**
     * @Given supplier :manufacturerReference named :name exists
     *
     * @param string $name
     * @param string $supplierReference
     */
    public function assertSupplierExistsByName(string $name, string $supplierReference): void
    {
        if ($supplierId = Supplier::getIdByName($name)) {
            $this->getSharedStorage()->set($supplierReference, $supplierId);

            return;
        }

        throw new RuntimeException(sprintf('Supplier %s named "%s" does not exist', $supplierReference, $name));
    }

    /**
     * @When I edit supplier :supplierReference with the following properties:
     *
     * @param string $supplierReference
     * @param TableNode $table
     */
    public function editSupplier(string $supplierReference, TableNode $table)
    {
        $supplierId = (int) $this->getSharedStorage()->get($supplierReference);
        $data = $this->localizeByRows($table);

        $editCommand = new EditSupplierCommand($supplierId);
        if (isset($data['name'])) {
            $editCommand->setName($data['name']);
        }
        if (isset($data['address'])) {
            $editCommand->setAddress($data['address']);
        }
        if (isset($data['address2'])) {
            $editCommand->setAddress2($data['address2']);
        }
        if (isset($data['post code'])) {
            $editCommand->setPostCode($data['post code']);
        }
        if (isset($data['city'])) {
            $editCommand->setCity($data['city']);
        }
        if (isset($data['state'])) {
            $editCommand->setStateId((int) State::getIdByName($data['state']));
        }
        if (isset($data['country'])) {
            $editCommand->setCountryId($this->getCountryIdByName($data['country']));
        }
        if (isset($data['dni'])) {
            $editCommand->setDni($data['dni']);
        }
        if (isset($data['phone'])) {
            $editCommand->setPhone($data['phone']);
        }
        if (isset($data['mobile phone'])) {
            $editCommand->setMobilePhone($data['mobile phone']);
        }
        if (isset($data['enabled'])) {
            $editCommand->setEnabled(PrimitiveUtils::castStringBooleanIntoBoolean($data['enabled']));
        }
        if (isset($data['description'])) {
            $editCommand->setLocalizedDescriptions($data['description']);
        }
        if (isset($data['meta title'])) {
            $editCommand->setLocalizedMetaTitles($data['meta title']);
        }
        if (isset($data['meta description'])) {
            $editCommand->setLocalizedMetaDescriptions($data['meta description']);
        }
        if (isset($data['shops'])) {
            $editCommand->setAssociatedShops($this->getShopIdsByReferences($data['shops']));
        }

        try {
            $this->getCommandBus()->handle($editCommand);
        } catch (SupplierException $e) {
            $this->setLastException($e);
        }

        if (isset($data['logo image'])) {
            $this->pretendImageUploaded(_PS_SUPP_IMG_DIR_, $data['logo image'], $supplierId);
        }
    }

    /**
     * @Given the supplier :supplierReference has a logo image
     *
     * @param string $supplierReference
     */
    public function assertSupplierHasLogoImage(string $supplierReference): void
    {
        $editableSupplier = $this->getEditableSupplier($supplierReference);
        Assert::assertNotNull($editableSupplier->getLogoImage());
    }

    /**
     * @Then the supplier :supplierReference does not have a logo image
     *
     * @param string $supplierReference
     */
    public function assertSupplierHasNoLogoImage(string $supplierReference): void
    {
        $editableSupplier = $this->getEditableSupplier($supplierReference);
        Assert::assertNull($editableSupplier->getLogoImage());
    }

    /**
     * @When I toggle status for supplier :supplierReference
     *
     * @param string $supplierReference
     *
     * @throws SupplierException
     */
    public function toggleSupplier(string $supplierReference): void
    {
        $this->getCommandBus()->handle(new ToggleSupplierStatusCommand($this->getSharedStorage()->get($supplierReference)));
    }

    /**
     * @When I delete supplier :supplierReference
     *
     * @param string $supplierReference
     *
     * @throws SupplierException
     */
    public function deleteSupplier(string $supplierReference): void
    {
        $this->getCommandBus()->handle(new DeleteSupplierCommand($this->getSharedStorage()->get($supplierReference)));
    }

    /**
     * @When I delete the supplier :supplierReference logo image
     *
     * @param string $supplierReference
     */
    public function deleteCategoryLogoImage(string $supplierReference): void
    {
        $supplierId = (int) $this->getSharedStorage()->get($supplierReference);

        $this->getCommandBus()->handle(new DeleteSupplierLogoImageCommand($supplierId));
    }

    /**
     * @Then supplier :supplierReference should have following properties:
     *
     * @param string $supplierReference
     * @param TableNode $table
     *
     * @throws SupplierException
     */
    public function assertSupplierProperties(string $supplierReference, TableNode $table)
    {
        $editableSupplier = $this->getEditableSupplier($supplierReference);
        $data = $this->localizeByRows($table);

        Assert::assertEquals($data['name'], $editableSupplier->getName(), 'Unexpected supplier name');
        Assert::assertEquals($data['address'], $editableSupplier->getAddress(), 'Unexpected supplier address');
        Assert::assertEquals($data['city'], $editableSupplier->getCity(), 'Unexpected supplier city');
        Assert::assertEquals(
            $this->getCountryIdByName($data['country']),
            $editableSupplier->getCountryId(),
            'Unexpected supplier country'
        );
        $expectedEnabled = PrimitiveUtils::castStringBooleanIntoBoolean($data['enabled']);
        Assert::assertEquals(
            $expectedEnabled,
            $editableSupplier->isEnabled(),
            sprintf('Expected supplier to be %s', $expectedEnabled ? 'enabled' : 'disabled')
        );
        Assert::assertEquals(
            $data['description'],
            $editableSupplier->getLocalizedDescriptions(),
            'Unexpected supplier localized descriptions'
        );
        Assert::assertEquals(
            $data['meta title'],
            $editableSupplier->getLocalizedMetaTitles(),
            'Unexpected supplier localized meta titles'
        );
        Assert::assertEquals(
            $data['meta description'],
            $editableSupplier->getLocalizedMetaDescriptions(),
            'Unexpected supplier localized meta descriptions'
        );
        Assert::assertEquals(
            $this->getShopIdsByReferences($data['shops']),
            $editableSupplier->getAssociatedShops(),
            'Unexpected supplier shops association'
        );

        if (isset($data['address2'])) {
            Assert::assertEquals($data['address2'], $editableSupplier->getAddress2(), 'Unexpected supplier address2');
        }

        if (isset($data['post code'])) {
            Assert::assertEquals($data['post code'], $editableSupplier->getPostCode(), 'Unexpected supplier post code');
        }

        if (isset($data['state'])) {
            Assert::assertEquals(
                (int) State::getIdByName($data['state']),
                $editableSupplier->getStateId(),
                'Unexpected supplier state'
            );
        }

        if (isset($data['phone'])) {
            Assert::assertEquals($data['phone'], $editableSupplier->getPhone(), 'Unexpected supplier phone');
        }

        if (isset($data['mobile phone'])) {
            Assert::assertEquals($data['mobile phone'], $editableSupplier->getPhone(), 'Unexpected supplier mobile phone');
        }

        if (isset($data['dni'])) {
            Assert::assertEquals($data['dni'], $editableSupplier->getDni(), 'Unexpected supplier DNI');
        }
    }

    /**
     * @param string $supplierReference
     *
     * @return EditableSupplier
     *
     * @throws SupplierException
     */
    private function getEditableSupplier(string $supplierReference): EditableSupplier
    {
        $supplierId = $this->getSharedStorage()->get($supplierReference);

        return $this->getQueryBus()->handle(new GetSupplierForEditing($supplierId));
    }

    /**
     * @param string $name
     *
     * @return int
     */
    private function getCountryIdByName(string $name): int
    {
        return Country::getIdByName((int) Configuration::get('PS_LANG_DEFAULT'), $name);
    }

    /**
     * @param string $shopReferencesAsString
     *
     * @return int[]
     */
    private function getShopIdsByReferences(string $shopReferencesAsString): array
    {
        $shopReferences = PrimitiveUtils::castStringArrayIntoArray($shopReferencesAsString);
        $shopIds = [];

        foreach ($shopReferences as $shopReference) {
            $shopIds[] = $this->getSharedStorage()->get($shopReference);
        }

        return $shopIds;
    }

    /**
     * @param string $reference
     * @param int|null $langId
     *
     * @return ViewableSupplier
     */
    private function getSupplierForViewing(string $reference, ?int $langId = null): ViewableSupplier
    {
        $langId = $langId ?? $this->getDefaultLangId();
        $supplierId = $this->getSharedStorage()->get($reference);

        /** @var ViewableSupplier $viewableSupplier */
        $viewableSupplier = $this->getQueryBus()->handle(new GetSupplierForViewing($supplierId, $langId));

        return $viewableSupplier;
    }

    /**
     * @param array<string, mixed> $productData
     * @param TableNode $expectedDataTable
     */
    private function assertProductFromViewableSupplier(array $productData, TableNode $expectedDataTable): void
    {
        $expectedData = $expectedDataTable->getColumnsHash();

        if (!empty($productData['combinations'])) {
            // combinations are indexed by combinationId, but for cleaner assertion we need to have simple index here
            $combinations = array_values($productData['combinations']);
            Assert::assertCount(count($expectedData), $combinations, 'Unexpected count of product combinations in viewable supplier');

            foreach ($expectedData as $key => $expectedRow) {
                $actualData = $combinations[$key];
                Assert::assertSame($actualData['attributes'], $expectedRow['attribute name']);
                $this->assertSupplierProductRow($actualData, $expectedRow);
            }

            return;
        }

        $expectedProductRow = reset($expectedData);
        $this->assertSupplierProductRow($productData, $expectedProductRow);
    }

    /**
     * @param array<string, mixed> $actualData
     * @param array<string, mixed> $expectedData
     */
    private function assertSupplierProductRow(array $actualData, array $expectedData): void
    {
        Assert::assertSame($actualData['supplier_reference'], $expectedData['supplier reference']);
        Assert::assertSame($actualData['wholesale_price'], $expectedData['wholesale price']);
        Assert::assertSame($actualData['ean13'], $expectedData['ean13']);
        Assert::assertSame($actualData['upc'], $expectedData['upc']);
        Assert::assertSame($actualData['quantity'], (int) $expectedData['quantity']);
    }
}
