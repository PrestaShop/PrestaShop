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
use PrestaShop\PrestaShop\Core\Domain\Product\AttributeGroup\Query\GetAttributeGroupList;
use PrestaShop\PrestaShop\Core\Domain\Product\AttributeGroup\Query\GetProductAttributeGroups;
use PrestaShop\PrestaShop\Core\Domain\Product\AttributeGroup\QueryResult\AttributeGroup;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use RuntimeException;
use Tests\Integration\Behaviour\Features\Context\Util\PrimitiveUtils;

class AttributeGroupFeatureContext extends AbstractDomainFeatureContext
{
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
    public function assertAllAttributeGroupsForShop(TableNode $tableNode, string $shopReferences): void
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
     * @Given there should be no attribute groups for shops ":shopReferences"
     *
     * @param string $shopReferences
     *
     * @return void
     */
    public function assertAttributeGroupsListForShopIsEmpty(string $shopReferences): void
    {
        $shopIds = $this->referencesToIds($shopReferences);

        foreach ($shopIds as $shopId) {
            $attributeGroups = $this->getQueryBus()->handle(new GetAttributeGroupList(
                ShopConstraint::shop($shopId),
                false
            ));
            $this->assertAttributeGroups([], $attributeGroups);
        }
    }

    /**
     * @Then product ":productReference" should have the following list of attribute groups:
     *
     * @param TableNode $tableNode
     */
    public function assertProductAttributeGroupsForDefaultShop(string $productReference, TableNode $tableNode): void
    {
        $attributeGroupsData = $this->localizeByColumns($tableNode);
        $productId = (int) $this->getSharedStorage()->get($productReference);
        $attributeGroups = $this->getQueryBus()->handle(new GetProductAttributeGroups(
            $productId,
            ShopConstraint::shop($this->getDefaultShopId()),
            false
        ));

        $this->assertAttributeGroups($attributeGroupsData, $attributeGroups);
    }

    private function assertAllAttributeGroups(TableNode $tableNode, ShopConstraint $shopConstraint): void
    {
        $attributeGroupsData = $this->localizeByColumns($tableNode);
        $attributeGroups = $this->getQueryBus()->handle(new GetAttributeGroupList(
            $shopConstraint,
            //@todo: get rid of this prop
            true
        ));

        $this->assertAttributeGroups($attributeGroupsData, $attributeGroups);
    }

    /**
     * @param array $expectedAttributeGroups
     * @param array $actualAttributeGroups
     */
    private function assertAttributeGroups(array $expectedAttributeGroups, array $actualAttributeGroups): void
    {
        Assert::assertEquals(count($expectedAttributeGroups), count($actualAttributeGroups));
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
            //@todo: missing assert for attributes
            // previously asserted null because of option $withAttributes, but we can get rid of it now
            // and I don;t think we will need to assert groups and attributes separately as we do not have that case in prod
//            Assert::assertNull($attributeGroup->getAttributes());

            $expectedId = $this->getSharedStorage()->get($attributeGroupsDatum['reference']);
            Assert::assertEquals($expectedId, $attributeGroup->getAttributeGroupId());
        }
    }

    /**
     * @Then product :productReference should have no attribute groups
     *
     * @param string $productReference
     */
    public function assertNoProductAttributesForDefaultShop(string $productReference): void
    {
        $attributeGroups = $this->getQueryBus()->handle(new GetProductAttributeGroups(
            (int) $this->getSharedStorage()->get($productReference),
            ShopConstraint::shop($this->getDefaultShopId()),
            false
        ));

        Assert::assertEmpty($attributeGroups);
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
    public function assertNoAttributesInGroupForShops(string $attributeGroupReference, string $shopReferences): void
    {
        $shopIds = $this->referencesToIds($shopReferences);

        foreach ($shopIds as $shopId) {
            $attributeGroups = $this->getQueryBus()->handle(new GetAttributeGroupList(
                ShopConstraint::shop($shopId),
                true
            ));

            $this->assertAttributesInGroups(
                [],
                $attributeGroups,
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
    public function assertAttributeInProductGroupsForDefaultShop(TableNode $tableNode, string $productReference, string $attributeGroupReference): void
    {
        $attributesData = $this->localizeByColumns($tableNode);
        $attributeGroups = $this->getQueryBus()->handle(new GetProductAttributeGroups(
            (int) $this->getSharedStorage()->get($productReference),
            ShopConstraint::shop($this->getDefaultShopId()),
            true
        ));

        $this->assertAttributesInGroups($attributesData, $attributeGroups, $attributeGroupReference);
    }

    private function assertAttributesInGroup(TableNode $tableNode, string $attributeGroupReference, ShopConstraint $shopConstraint): void
    {
        $attributesData = $this->localizeByColumns($tableNode);
        $attributeGroups = $this->getQueryBus()->handle(new GetAttributeGroupList(
            $shopConstraint,
            true
        ));

        $this->assertAttributesInGroups($attributesData, $attributeGroups, $attributeGroupReference);
    }

    /**
     * @param array $expectedAttributesData
     * @param array $attributeGroups
     * @param string $attributeGroupReference
     */
    private function assertAttributesInGroups(array $expectedAttributesData, array $attributeGroups, string $attributeGroupReference): void
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

        Assert::assertEquals(count($expectedAttributesData), count($checkAttributeGroup->getAttributes()));
        $attributes = $checkAttributeGroup->getAttributes();
        foreach ($expectedAttributesData as $index => $attributesDatum) {
            $attribute = $attributes[$index];
            Assert::assertEquals($attributesDatum['color'], $attribute->getColor());
            Assert::assertEquals($attributesDatum['position'], $attribute->getPosition());

            $attributeNames = $attribute->getLocalizedNames();
            foreach ($attributesDatum['name'] as $langId => $name) {
                Assert::assertTrue(isset($attributeNames[$langId]));
                Assert::assertEquals($name, $attributeNames[$langId]);
            }

            $expectedId = $this->getSharedStorage()->get($attributesDatum['reference']);
            Assert::assertEquals($expectedId, $attribute->getAttributeId());
        }
    }
}
