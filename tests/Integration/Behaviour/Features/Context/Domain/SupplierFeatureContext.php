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
use PrestaShop\PrestaShop\Core\Domain\Supplier\Exception\SupplierException;
use PrestaShop\PrestaShop\Core\Domain\Supplier\Query\GetSupplierForEditing;
use PrestaShop\PrestaShop\Core\Domain\Supplier\QueryResult\EditableSupplier;
use PrestaShop\PrestaShop\Core\Domain\Supplier\ValueObject\SupplierId;
use State;
use Tests\Integration\Behaviour\Features\Context\Util\PrimitiveUtils;

class SupplierFeatureContext extends AbstractDomainFeatureContext
{
    /**
     * @When I add new supplier :supplierReference with following properties:
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
                $data['meta keywords'],
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
            $data['meta keywords'],
            $editableSupplier->getLocalizedMetaKeywords(),
            'Unexpected supplier localized meta keywords'
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
        return Country::getIdByName(Configuration::get('PS_LANG_DEFAULT'), $name);
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
}
