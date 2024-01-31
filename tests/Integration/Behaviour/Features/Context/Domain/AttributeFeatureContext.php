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
use PHPUnit\Framework\Assert;
use PrestaShop\PrestaShop\Core\Domain\AttributeGroup\Attribute\Command\AddAttributeCommand;
use PrestaShop\PrestaShop\Core\Domain\AttributeGroup\Attribute\Command\DeleteAttributeCommand;
use PrestaShop\PrestaShop\Core\Domain\AttributeGroup\Attribute\Command\EditAttributeCommand;
use PrestaShop\PrestaShop\Core\Domain\AttributeGroup\Attribute\Exception\AttributeConstraintException;
use PrestaShop\PrestaShop\Core\Domain\AttributeGroup\Attribute\Exception\AttributeNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\AttributeGroup\Attribute\Query\GetAttributeForEditing;
use PrestaShop\PrestaShop\Core\Domain\AttributeGroup\Attribute\QueryResult\EditableAttribute;
use PrestaShop\PrestaShop\Core\Domain\AttributeGroup\Attribute\ValueObject\AttributeId;
use PrestaShop\PrestaShop\Core\Domain\AttributeGroup\Exception\AttributeGroupConstraintException;
use Tests\Integration\Behaviour\Features\Context\Util\NoExceptionAlthoughExpectedException;

class AttributeFeatureContext extends AbstractDomainFeatureContext
{
    /**
     * @When I create attribute :reference with specified properties:
     */
    public function createAttribute(string $reference, TableNode $node): void
    {
        $properties = $this->localizeByRows($node);

        $attributeGroupId = $this->referenceToId($properties['attribute_group']);
        $attributeId = $this->createAttributeUsingCommand(
            $attributeGroupId,
            $properties['value'],
            $properties['color'],
            $this->referencesToIds($properties['shopIds'])
        );

        $this->getSharedStorage()->set($reference, $attributeId->getValue());
    }

    /**
     * @When I create attribute :reference with invalid color I should get an exception:
     */
    public function createAttributeWithInvalidColor(string $reference, TableNode $node): void
    {
        $caughtException = null;
        $properties = $this->localizeByRows($node);
        $attributeGroupId = $this->referenceToId($properties['attribute_group']);

        try {
            $this->createAttributeUsingCommand(
                $attributeGroupId,
                $properties['value'],
                $properties['color'],
                $this->referencesToIds($properties['shopIds'])
            );
        } catch (AttributeConstraintException $e) {
            $caughtException = $e;
        }

        Assert::assertNotNull(
            $caughtException,
            sprintf('Creating an attribute with invalid color %s should trigger an exception', $properties['color'])
        );
        Assert::assertEquals(
            $caughtException->getCode(),
            AttributeConstraintException::INVALID_COLOR,
            sprintf('The thrown exception for an invalid color should have the code %d', AttributeConstraintException::INVALID_COLOR)
        );
    }

    /**
     * @When I edit attribute :reference with specified properties:
     */
    public function editAttribute(string $reference, TableNode $node): void
    {
        $properties = $this->localizeByRows($node);

        $attributeId = $this->referenceToId($reference);
        $attributeGroupId = $this->referenceToId($properties['attribute_group']);
        $this->editAttributeUsingCommand(
            $attributeId,
            $attributeGroupId,
            $properties['value'],
            $properties['color'],
            $this->referencesToIds($properties['shopIds'])
        );
    }

    /**
     * @Then attribute :reference should have the following properties:
     *
     * @param string $reference
     * @param TableNode $tableNode
     */
    public function assertAttributeGroupProperties(string $reference, TableNode $tableNode): void
    {
        $attribute = $this->getAttribute($reference);
        $data = $this->localizeByRows($tableNode);
        $attributeGroupId = $this->referenceToId($data['attribute_group']);
        Assert::assertEquals($data['value'], $attribute->getValue());
        Assert::assertEquals($data['color'], $attribute->getColor());
        Assert::assertEquals($attributeGroupId, $attribute->getAttributeGroupId());
        Assert::assertEquals($this->referencesToIds($data['shopIds']), $attribute->getAssociatedShopIds());
    }

    /**
     * @param int $attributeGroupId
     * @param array $localizedValues
     * @param string $color
     *
     * @return AttributeId
     *
     * @throws AttributeConstraintException
     */
    private function createAttributeUsingCommand(
        int $attributeGroupId,
        array $localizedValues,
        string $color,
        array $shopIds
    ): AttributeId {
        $command = new AddAttributeCommand(
            $attributeGroupId,
            $localizedValues,
            $color,
            $shopIds
        );

        return $this->getCommandBus()->handle($command);
    }

    /**
     * @param int $attributeId
     * @param int $attributeGroupId
     * @param array $localizedValue
     * @param string $color
     *
     * @return void
     *
     * @throws AttributeGroupConstraintException
     * @throws AttributeConstraintException
     */
    private function editAttributeUsingCommand(
        int $attributeId,
        int $attributeGroupId,
        array $localizedValue,
        string $color,
        array $shopIds
    ): void {
        $command = new EditAttributeCommand($attributeId);
        $command->setAttributeGroupId($attributeGroupId);
        $command->setLocalizedValue($localizedValue);
        $command->setColor($color);
        $command->setAssociatedShopIds($shopIds);

        $this->getCommandBus()->handle($command);
    }

    /**
     * @When I delete attribute :reference
     */
    public function deleteAttribute(string $reference): void
    {
        $attributeId = $this->referenceToId($reference);

        $this->getCommandBus()->handle(new DeleteAttributeCommand($attributeId));
    }

    /**
     * @Then attribute :reference should be deleted
     */
    public function assertAttributeIsDeleted(string $reference): void
    {
        $attributeId = $this->referenceToId($reference);

        try {
            $this->getQueryBus()->handle(new GetAttributeForEditing($attributeId));

            throw new NoExceptionAlthoughExpectedException(sprintf('Attribute %s exists, but it was expected to be deleted', $reference));
        } catch (AttributeNotFoundException $e) {
            $this->getSharedStorage()->clear($reference);
        }
    }

    /**
     * @param string $reference
     *
     * @return EditableAttribute
     */
    private function getAttribute(string $reference): EditableAttribute
    {
        $id = $this->referenceToId($reference);

        return $this->getCommandBus()->handle(new GetAttributeForEditing($id));
    }
}
