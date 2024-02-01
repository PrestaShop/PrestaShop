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
        try {
            $attributeId = $this->createAttributeUsingCommand(
                $attributeGroupId,
                $properties['name'],
                $properties['color'],
                $this->referencesToIds($properties['shopIds'])
            );

            $this->getSharedStorage()->set($reference, $attributeId->getValue());
        } catch (\Exception $e) {
            $this->setLastException($e);
        }
    }

    /**
     * @Then I should get an error that color value is invalid
     */
    public function IShouldGetAnInvalidColorError(): void
    {
        $this->assertLastErrorIs(
            AttributeConstraintException::class,
            AttributeConstraintException::INVALID_COLOR
        );
    }

    /**
     * @When I edit attribute :reference with specified properties:
     */
    public function editAttribute(string $reference, TableNode $node): void
    {
        $properties = $this->localizeByRows($node);
        $attributeId = $this->referenceToId($reference);

        $command = new EditAttributeCommand($attributeId);
        if (isset($properties['attribute_group'])) {
            $command->setAttributeGroupId($this->referenceToId($properties['attribute_group']));
        }
        if (isset($properties['name'])) {
            $command->setLocalizedNames($properties['name']);
        }
        if (isset($properties['color'])) {
            $command->setColor($properties['color']);
        }
        if (isset($properties['shopIds'])) {
            $command->setAssociatedShopIds($this->referencesToIds($properties['shopIds']));
        }

        try {
            $this->getCommandBus()->handle($command);
        } catch (AttributeConstraintException $e) {
            $this->setLastException($e);
        }
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
        if (isset($data['name'])) {
            Assert::assertEquals($data['name'], $attribute->getLocalizedNames());
        }
        if (isset($data['color'])) {
            Assert::assertEquals($data['color'], $attribute->getColor());
        }
        if (isset($data['attribute_group'])) {
            Assert::assertEquals($this->referenceToId($data['attribute_group']), $attribute->getAttributeGroupId());
        }
        if (isset($data['shopIds'])) {
            Assert::assertEquals($this->referencesToIds($data['shopIds']), $attribute->getAssociatedShopIds());
        }
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
