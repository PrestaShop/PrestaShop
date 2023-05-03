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

namespace Tests\Integration\Behaviour\Features\Context\Domain;

use Behat\Gherkin\Node\TableNode;
use PrestaShop\PrestaShop\Core\Domain\AttributeGroup\Attribute\Command\AddAttributeCommand;
use PrestaShop\PrestaShop\Core\Domain\AttributeGroup\Attribute\Command\EditAttributeCommand;
use PrestaShop\PrestaShop\Core\Domain\AttributeGroup\Attribute\Exception\AttributeNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\AttributeGroup\Attribute\Query\GetAttributeForEditing;
use PrestaShop\PrestaShop\Core\Domain\AttributeGroup\Attribute\ValueObject\AttributeId;
use PrestaShop\PrestaShop\Core\Domain\AttributeGroup\Exception\AttributeGroupConstraintException;
use RuntimeException;
use Tests\Integration\Behaviour\Features\Context\CommonFeatureContext;
use Tests\Integration\Behaviour\Features\Context\SharedStorage;
use Tests\Integration\Behaviour\Features\Context\Util\NoExceptionAlthoughExpectedException;

class AttributeFeatureContext extends AbstractDomainFeatureContext
{
    /**
     * @var int default language id from configs
     */
    private $defaultLangId;

    /**
     * @var int default shop id from configs
     */
    private $defaultShopId;

    public function __construct()
    {
        $configuration = CommonFeatureContext::getContainer()->get('prestashop.adapter.legacy.configuration');
        $this->defaultLangId = $configuration->get('PS_LANG_DEFAULT');
        $this->defaultShopId = $configuration->get('PS_SHOP_DEFAULT');
    }

    /**
     * @When I create attribute :reference with specified properties:
     */
    public function createAttribute(string $reference, TableNode $node): void
    {
        $properties = $node->getRowsHash();
        $attributeId = $this->createAttributeUsingCommand($properties['attribute_group_id'], $properties['value'], $properties['color']);

        $this->getSharedStorage()->set($reference, $attributeId->getValue());
    }

    /**
     * @When I edit attribute :reference with specified properties:
     */
    public function editAttribute(string $reference, TableNode $node): void
    {
        $attributeId = SharedStorage::getStorage()->get($reference);

        $properties = $node->getRowsHash();
        $this->editAttributeUsingCommand($attributeId, $properties['attribute_group_id'], $properties['value'], $properties['color']);
    }

    /**
     * @Then attribute  :reference should be deleted
     */
    public function assertAttributeIsDeleted(string $reference): void
    {
        $attributeId = SharedStorage::getStorage()->get($reference);

        try {
            $this->getQueryBus()->handle(new GetAttributeForEditing($attributeId));

            throw new NoExceptionAlthoughExpectedException(sprintf('Attribute %s exists, but it was expected to be deleted', $reference));
        } catch (AttributeNotFoundException $e) {
            SharedStorage::getStorage()->clear($reference);
        }
    }

    /**
     * @Then attribute :reference :field should be :value
     */
    public function assertFieldValue(string $reference, string $field, string $value): void
    {
        $attributeId = SharedStorage::getStorage()->get($reference);
        $attribute = new \Attribute($attributeId);

        if ($attribute->$field !== $value) {
            throw new RuntimeException(sprintf('Attribute "%s" has "%s" %s, but "%s" was expected.', $reference, $attribute->$field, $field, $value));
        }
    }

    /**
     * @Then attribute :reference :field in default language should be :value
     */
    public function assertFieldWithLangValue(string $reference, string $field, string $value): void
    {
        $attributeId = SharedStorage::getStorage()->get($reference);
        $attribute = new \Attribute($attributeId);

        if ($attribute->$field[$this->defaultLangId] !== $value) {
            throw new RuntimeException(sprintf('Attribute  "%s" has "%s" %s, but "%s" was expected.', $reference, $attribute->$field[$this->defaultLangId], $field, $value));
        }
    }

    /**
     * @param int $attributeGroupId
     * @param string $valueDefaultLanguage
     * @param string $color
     *
     * @return AttributeId
     */
    private function createAttributeUsingCommand(
        int $attributeGroupId,
        string $valueDefaultLanguage,
        string $color
    ): AttributeId {
        $command = new AddAttributeCommand(
            $attributeGroupId,
            [$this->defaultLangId => $valueDefaultLanguage],
            $color,
            [$this->defaultShopId]
        );

        return $this->getCommandBus()->handle($command);
    }

    /**
     * @param int $attributeId
     * @param int $attributeGroupId
     * @param string $valueDefaultLanguage
     * @param string $color
     *
     * @return AttributeId
     *
     * @throws AttributeGroupConstraintException
     */
    private function editAttributeUsingCommand(
        int $attributeId,
        int $attributeGroupId,
        string $valueDefaultLanguage,
        string $color
    ): AttributeId {
        $command = new EditAttributeCommand(
            $attributeId,
            $attributeGroupId,
            [$this->defaultLangId => $valueDefaultLanguage],
            $color,
            [$this->defaultShopId]
        );

        return $this->getCommandBus()->handle($command);
    }
}
