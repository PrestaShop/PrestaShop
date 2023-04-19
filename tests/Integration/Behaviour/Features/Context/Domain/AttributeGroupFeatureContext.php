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
use PrestaShop\PrestaShop\Core\Domain\AttributeGroup\Command\AddAttributeGroupCommand;
use PrestaShop\PrestaShop\Core\Domain\AttributeGroup\Command\DeleteAttributeGroupCommand;
use PrestaShop\PrestaShop\Core\Domain\AttributeGroup\Command\EditAttributeGroupCommand;
use PrestaShop\PrestaShop\Core\Domain\AttributeGroup\Exception\AttributeGroupConstraintException;
use PrestaShop\PrestaShop\Core\Domain\AttributeGroup\Exception\AttributeGroupNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\AttributeGroup\Exception\InvalidAttributeGroupTypeException;
use PrestaShop\PrestaShop\Core\Domain\AttributeGroup\Query\GetAttributeGroupForEditing;
use PrestaShop\PrestaShop\Core\Domain\AttributeGroup\Query\GetAttributeGroupList;
use PrestaShop\PrestaShop\Core\Domain\AttributeGroup\QueryResult\AttributeGroup;
use PrestaShop\PrestaShop\Core\Domain\AttributeGroup\QueryResult\EditableAttributeGroup;
use PrestaShop\PrestaShop\Core\Domain\AttributeGroup\ValueObject\AttributeGroupId;
use PrestaShop\PrestaShop\Core\Domain\AttributeGroup\ValueObject\AttributeGroupType;
use PrestaShop\PrestaShop\Core\Domain\Product\AttributeGroup\Query\GetProductAttributeGroups;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use RuntimeException;
use Tests\Integration\Behaviour\Features\Context\SharedStorage;
use Tests\Integration\Behaviour\Features\Context\Util\NoExceptionAlthoughExpectedException;
use Tests\Integration\Behaviour\Features\Context\Util\PrimitiveUtils;

class AttributeGroupFeatureContext extends AbstractDomainFeatureContext
{
    /**
     * @When I create attribute group :reference with specified properties:
     */
    public function createAttributeGroup(string $reference, TableNode $node): void
    {
        $data = $this->localizeByRows($node);

        $attributeGroupId = $this->createAttributeGroupUsingCommand($data['name'], $data['public_name'], $data['type']);

        $this->getSharedStorage()->set($reference, $attributeGroupId->getValue());
    }

    /**
     * @When I edit attribute group :reference with specified properties:
     */
    public function editAttributeGroup(string $reference, TableNode $node): void
    {
        $attributeGroupId = $this->referenceToId($reference);
        $data = $this->localizeByRows($node);

        $this->editAttributeGroupUsingCommand($attributeGroupId, $data['name'], $data['public_name'], $data['type']);
    }

    /**
     * @When I delete attribute group :reference
     */
    public function deleteManufacturer(string $reference): void
    {
        $attributeGroupId = $this->referenceToId($reference);

        $this->getCommandBus()->handle(new DeleteAttributeGroupCommand($attributeGroupId));
    }

    /**
     * @Then attribute group :reference should be deleted
     */
    public function assertAttributeGroupIsDeleted(string $reference): void
    {
        $attributeGroupId = $this->referenceToId($reference);

        try {
            $this->getQueryBus()->handle(new GetAttributeGroupForEditing($attributeGroupId));

            throw new NoExceptionAlthoughExpectedException(sprintf('Attribute group %s exists, but it was expected to be deleted', $reference));
        } catch (AttributeGroupNotFoundException $e) {
            $this->getSharedStorage()->clear($reference);
        }
    }

    /**
     * @Then attribute group :reference :field should be :value
     */
    public function assertFieldValue(string $reference, string $field, string $value): void
    {
        $attributeGroupId = SharedStorage::getStorage()->get($reference);
        $attributeGroup = new \AttributeGroup($attributeGroupId);

        if ($attributeGroup->$field !== $value) {
            throw new RuntimeException(sprintf('Attribute group "%s" has "%s" %s, but "%s" was expected.', $reference, $attributeGroup->$field, $field, $value));
        }
    }

    /**
     * @Then attribute group :reference :field in default language should be :value
     */
    public function assertFieldWithLangValue(string $reference, string $field, string $value): void
    {
        $attributeGroupId = $this->referenceToId($reference);
        $attributeGroup = new \AttributeGroup($attributeGroupId);

        if ($attributeGroup->$field[$this->getDefaultLangId()] !== $value) {
            throw new RuntimeException(sprintf('Attribute group "%s" has "%s" %s, but "%s" was expected.', $reference, $attributeGroup->$field[$this->getDefaultLangId()], $field, $value));
        }
    }

    /**
     * @Then attribute group :reference should have the following properties:
     *
     * @param string $reference
     * @param TableNode $tableNode
     */
    public function assertAttributeGroupProperties(string $reference, TableNode $tableNode): void
    {
        $attributeGroup = $this->getAttributeGroup($reference);
        $data = $this->localizeByRows($tableNode);

        Assert::assertEquals($data['name'], $attributeGroup->getName());
        Assert::assertEquals($data['public_name'], $attributeGroup->getPublicName());
        Assert::assertEquals($data['type'], $attributeGroup->getType());
    }

    /**
     * @Given there is a list of following attribute groups:
     *
     * @param TableNode $tableNode
     */
    public function assertAllAttributeGroupsForDefaultShop(TableNode $tableNode): void
    {
        $this->assertAllAttributeGroups($tableNode, ShopConstraint::shop($this->getDefaultShopId()));
    }

    /**
     * @Given there is a list of following attribute groups for shops ":shopReferences":
     *
     * @param TableNode $tableNode
     * @param string $shopReferences
     */
    public function assertAllAttributeGroupsForShops(TableNode $tableNode, string $shopReferences): void
    {
        $shopIds = $this->referencesToIds($shopReferences);

        foreach ($shopIds as $shopId) {
            $this->assertAllAttributeGroups(
                $tableNode,
                ShopConstraint::shop($shopId)
            );
        }
    }

    /**
     * @Given there is a list of following attribute groups for all shops:
     *
     * @param TableNode $tableNode
     */
    public function assertAllAttributeGroupsForAllShops(TableNode $tableNode): void
    {
        $this->assertAllAttributeGroups(
            $tableNode,
            ShopConstraint::allShops()
        );
    }

    /**
     * @Given there should be no attribute groups for shops ":shopReferences"
     *
     * @param string $shopReferences
     *
     * @return void
     */
    public function assertAllAttributeGroupsListForShopIsEmpty(string $shopReferences): void
    {
        $shopIds = $this->referencesToIds($shopReferences);

        foreach ($shopIds as $shopId) {
            $attributeGroups = $this->getQueryBus()->handle(new GetAttributeGroupList(
                ShopConstraint::shop($shopId)
            ));

            Assert::assertEmpty(
                $attributeGroups,
                sprintf('Expected no attribute groups to be found for shop id %d', $shopId)
            );
        }
    }

    /**
     * @Then product ":productReference" should have the following list of attribute groups:
     *
     * @param TableNode $tableNode
     */
    public function assertProductAttributeGroupsForDefaultShop(string $productReference, TableNode $tableNode): void
    {
        $this->assertProductAttributeGroups($productReference, $tableNode, ShopConstraint::shop($this->getDefaultShopId()));
    }

    /**
     * @Then product ":productReference" should have the following list of attribute groups for shops ":shopReferences":
     *
     * @param TableNode $tableNode
     */
    public function assertProductAttributeGroupsForShops(string $productReference, TableNode $tableNode, string $shopReferences): void
    {
        $shopIds = $this->referencesToIds($shopReferences);

        foreach ($shopIds as $shopId) {
            $this->assertProductAttributeGroups($productReference, $tableNode, ShopConstraint::shop($shopId));
        }
    }

    /**
     * @Then product ":productReference" should have the following list of attribute groups for all shops:
     *
     * @param TableNode $tableNode
     */
    public function assertProductAttributeGroupsForAllShops(string $productReference, TableNode $tableNode): void
    {
        $this->assertProductAttributeGroups($productReference, $tableNode, ShopConstraint::allShops());
    }

    /**
     * @Then product :productReference should have no attribute groups
     *
     * @param string $productReference
     */
    public function assertNoProductAttributesForDefaultShop(string $productReference): void
    {
        $shopId = $this->getDefaultShopId();
        $attributeGroups = $this->getQueryBus()->handle(new GetProductAttributeGroups(
            (int) $this->getSharedStorage()->get($productReference),
            ShopConstraint::shop($shopId)
        ));

        Assert::assertEmpty(
            $attributeGroups,
            sprintf('Expected no attribute groups to be found for product "%s" and shop id %d', $productReference, $shopId)
        );
    }

    /**
     * @Given the attribute group :attributeGroupReference should have the following attributes:
     *
     * @param TableNode $tableNode
     * @param string $attributeGroupReference
     */
    public function assertAttributesInGroupForDefaultShop(TableNode $tableNode, string $attributeGroupReference): void
    {
        $this->assertAttributesInGroup(
            $tableNode,
            $attributeGroupReference,
            ShopConstraint::shop($this->getDefaultShopId())
        );
    }

    /**
     * @Given the attribute group :attributeGroupReference should have the following attributes for all shops:
     *
     * @param TableNode $tableNode
     * @param string $attributeGroupReference
     */
    public function assertAttributesInGroupForAllShops(TableNode $tableNode, string $attributeGroupReference): void
    {
        $this->assertAttributesInGroup(
            $tableNode,
            $attributeGroupReference,
            ShopConstraint::allShops()
        );
    }

    /**
     * @Given the attribute group :attributeGroupReference should have the following attributes for shops ":shopReferences":
     *
     * @param TableNode $tableNode
     * @param string $attributeGroupReference
     * @param string $shopReferences
     */
    public function assertAttributesInGroupForShops(
        TableNode $tableNode,
        string $attributeGroupReference,
        string $shopReferences
    ): void {
        $shopIds = $this->referencesToIds($shopReferences);

        foreach ($shopIds as $shopId) {
            $this->assertAttributesInGroup(
                $tableNode,
                $attributeGroupReference,
                ShopConstraint::shop($shopId)
            );
        }
    }

    /**
     * @Then the attribute group ":attributeGroupReference" should have no attributes for shops ":shopReferences"
     *
     * @param string $attributeGroupReference
     * @param string $shopReferences
     *
     * @return void
     */
    public function assertAttributesAreEmptyInGroupForShops(string $attributeGroupReference, string $shopReferences): void
    {
        $shopIds = $this->referencesToIds($shopReferences);

        foreach ($shopIds as $shopId) {
            $this->performAttributesInGroupAssertion(
                null,
                $this->getQueryBus()->handle(new GetAttributeGroupList(ShopConstraint::shop($shopId))),
                $attributeGroupReference
            );
        }
    }

    /**
     * @Then product ":productReference" should have the following list of attributes in attribute group ":attributeGroupReference":
     *
     * @param TableNode $tableNode
     * @param string $productReference
     * @param string $attributeGroupReference
     */
    public function assertProductAttributesInGroupForDefaultShop(TableNode $tableNode, string $productReference, string $attributeGroupReference): void
    {
        $attributeGroups = $this->getQueryBus()->handle(new GetProductAttributeGroups(
            (int) $this->getSharedStorage()->get($productReference),
            ShopConstraint::shop($this->getDefaultShopId())
        ));

        $this->performAttributesInGroupAssertion($tableNode, $attributeGroups, $attributeGroupReference);
    }

    /**
     * @Then product ":productReference" should have the following list of attributes in attribute group ":attributeGroupReference" for shops ":shopReferences":
     *
     * @param TableNode $tableNode
     * @param string $productReference
     * @param string $attributeGroupReference
     * @param string $shopReferences
     */
    public function assertProductAttributesInGroupForShops(
        TableNode $tableNode,
        string $productReference,
        string $attributeGroupReference,
        string $shopReferences
    ): void {
        $shopIds = $this->referencesToIds($shopReferences);

        foreach ($shopIds as $shopId) {
            $this->assertProductAttributesInGroup(
                $tableNode,
                $productReference,
                $attributeGroupReference,
                ShopConstraint::shop($shopId)
            );
        }
    }

    /**
     * @Then product ":productReference" should have the following list of attributes in attribute group ":attributeGroupReference" for all shops:
     *
     * @param TableNode $tableNode
     * @param string $productReference
     * @param string $attributeGroupReference
     */
    public function assertProductAttributesInGroupForAllShops(
        TableNode $tableNode,
        string $productReference,
        string $attributeGroupReference
    ): void {
        $this->assertProductAttributesInGroup(
            $tableNode,
            $productReference,
            $attributeGroupReference,
            ShopConstraint::allShops()
        );
    }

    private function assertAttributesInGroup(TableNode $tableNode, string $attributeGroupReference, ShopConstraint $shopConstraint): void
    {
        $attributeGroups = $this->getQueryBus()->handle(new GetAttributeGroupList($shopConstraint));

        $this->performAttributesInGroupAssertion($tableNode, $attributeGroups, $attributeGroupReference);
    }

    private function assertProductAttributesInGroup(
        TableNode $tableNode,
        string $productReference,
        string $attributeGroupReference,
        ShopConstraint $shopConstraint
    ): void {
        $this->performAttributesInGroupAssertion(
            $tableNode,
            $this->getQueryBus()->handle(new GetProductAttributeGroups(
                $this->getSharedStorage()->get($productReference),
                $shopConstraint
            )),
            $attributeGroupReference
        );
    }

    private function assertAllAttributeGroups(TableNode $tableNode, ShopConstraint $shopConstraint): void
    {
        $this->performAttributeGroupsAssertion(
            $tableNode,
            $this->getQueryBus()->handle(new GetAttributeGroupList($shopConstraint))
        );
    }

    private function assertProductAttributeGroups(string $productReference, TableNode $tableNode, ShopConstraint $shopConstraint): void
    {
        $this->performAttributeGroupsAssertion(
            $tableNode,
            $this->getQueryBus()->handle(new GetProductAttributeGroups(
                (int) $this->getSharedStorage()->get($productReference),
                $shopConstraint
            ))
        );
    }

    /**
     * @param TableNode $tableNode
     * @param array $actualAttributeGroups
     */
    private function performAttributeGroupsAssertion(TableNode $tableNode, array $actualAttributeGroups): void
    {
        $expectedAttributeGroups = $this->localizeByColumns($tableNode);

        Assert::assertEquals(
            count($expectedAttributeGroups),
            count($actualAttributeGroups),
            'Expected count of attribute groups doesn\'t match'
        );
        foreach ($expectedAttributeGroups as $index => $attributeGroupsDatum) {
            /** @var AttributeGroup $attributeGroup */
            $attributeGroup = $actualAttributeGroups[$index];
            Assert::assertEquals(PrimitiveUtils::castStringBooleanIntoBoolean($attributeGroupsDatum['is_color_group']), $attributeGroup->isColorGroup());
            Assert::assertEquals($attributeGroupsDatum['group_type'], $attributeGroup->getGroupType());
            Assert::assertEquals($attributeGroupsDatum['position'], $attributeGroup->getPosition());

            $attributeGroupNames = $attributeGroup->getLocalizedNames();
            foreach ($attributeGroupsDatum['name'] as $langId => $name) {
                Assert::assertTrue(isset($attributeGroupNames[$langId]));
                Assert::assertEquals($name, $attributeGroupNames[$langId]);
            }

            $attributeGroupPublicNames = $attributeGroup->getLocalizedPublicNames();
            foreach ($attributeGroupsDatum['public_name'] as $langId => $publicName) {
                Assert::assertTrue(isset($attributeGroupPublicNames[$langId]));
                Assert::assertEquals($publicName, $attributeGroupPublicNames[$langId]);
            }

            $expectedId = $this->getSharedStorage()->get($attributeGroupsDatum['reference']);
            Assert::assertEquals($expectedId, $attributeGroup->getAttributeGroupId());
        }
    }

    /**
     * @param TableNode|null $tableNode when null is passed, it means we expect empty result
     * @param array $attributeGroups
     * @param string $attributeGroupReference
     */
    private function performAttributesInGroupAssertion(?TableNode $tableNode, array $attributeGroups, string $attributeGroupReference): void
    {
        $attributeGroupId = $this->getSharedStorage()->get($attributeGroupReference);
        $checkAttributeGroup = null;
        /** @var AttributeGroup $attributeGroup */
        foreach ($attributeGroups as $attributeGroup) {
            if ($attributeGroup->getAttributeGroupId() === $attributeGroupId) {
                $checkAttributeGroup = $attributeGroup;
                break;
            }
        }

        if (null === $checkAttributeGroup) {
            throw new RuntimeException(sprintf('Could no find attribute group %s', $attributeGroupReference));
        }

        if (!$tableNode) {
            // if tableNode is null we expect that desired group attributes are empty
            Assert::assertEmpty(
                $checkAttributeGroup->getAttributes(),
                sprintf('Expected no attributes in group "%s"', $attributeGroupReference)
            );

            return;
        }

        $expectedAttributesData = $this->localizeByColumns($tableNode);
        Assert::assertEquals(count($expectedAttributesData), count($checkAttributeGroup->getAttributes()));
        $attributes = $checkAttributeGroup->getAttributes();
        foreach ($expectedAttributesData as $index => $attributesDatum) {
            $attribute = $attributes[$index];
            Assert::assertEquals($attributesDatum['color'], $attribute->getColor(), 'Unexpected color');
            Assert::assertEquals($attributesDatum['position'], $attribute->getPosition(), 'Unexpected position');

            $attributeNames = $attribute->getLocalizedNames();
            foreach ($attributesDatum['name'] as $langId => $name) {
                Assert::assertTrue(isset($attributeNames[$langId]));
                Assert::assertEquals($name, $attributeNames[$langId], 'Unexpected name');
            }

            $expectedId = $this->getSharedStorage()->get($attributesDatum['reference']);
            Assert::assertEquals($expectedId, $attribute->getAttributeId());
        }
    }

    /**
     * @param array<int, string> $localizedNames
     * @param array<int, string> $localizedPublicNames
     * @param string $type
     *
     * @return AttributeGroupId
     *
     * @throws AttributeGroupConstraintException
     * @throws InvalidAttributeGroupTypeException
     */
    private function createAttributeGroupUsingCommand(array $localizedNames, array $localizedPublicNames, string $type): AttributeGroupId
    {
        $command = new AddAttributeGroupCommand(
            $localizedNames,
            $localizedPublicNames,
            (new AttributeGroupType($type)),
            [$this->getDefaultShopId()]
        );

        return $this->getCommandBus()->handle($command);
    }

    /**
     * @param int $attributeGroupId
     * @param array<int, string> $localizedNames
     * @param array<int, string> $localizedPublicNames
     * @param string $type
     *
     * @return void
     *
     * @throws AttributeGroupConstraintException
     * @throws InvalidAttributeGroupTypeException
     */
    private function editAttributeGroupUsingCommand(
        int $attributeGroupId,
        array $localizedNames,
        array $localizedPublicNames,
        string $type
    ): void {
        $command = new EditAttributeGroupCommand(
            $attributeGroupId,
            $localizedNames,
            $localizedPublicNames,
            (new AttributeGroupType($type)),
            [$this->getDefaultShopId()]
        );

        $this->getCommandBus()->handle($command);
    }

    /**
     * @param string $reference
     *
     * @return EditableAttributeGroup
     */
    private function getAttributeGroup(string $reference): EditableAttributeGroup
    {
        $id = $this->referenceToId($reference);

        return $this->getCommandBus()->handle(new GetAttributeGroupForEditing($id));
    }
}
