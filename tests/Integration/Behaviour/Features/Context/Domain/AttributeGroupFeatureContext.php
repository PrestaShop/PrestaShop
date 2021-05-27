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

use AttributeGroup as LegacyAttributeGroup;
use Behat\Gherkin\Node\TableNode;
use Language;
use PHPUnit\Framework\Assert;
use PrestaShop\PrestaShop\Core\Domain\Product\AttributeGroup\Query\GetAttributeGroupList;
use PrestaShop\PrestaShop\Core\Domain\Product\AttributeGroup\Query\GetProductAttributeGroups;
use PrestaShop\PrestaShop\Core\Domain\Product\AttributeGroup\QueryResult\AttributeGroup;
use RuntimeException;
use Tests\Integration\Behaviour\Features\Context\Util\PrimitiveUtils;

class AttributeGroupFeatureContext extends AbstractDomainFeatureContext
{
    /**
     * @Given attribute group :reference named :name in :langIso language exists
     *
     * @param string $attributeGroupReference
     * @param string $name
     * @param string $langIso
     */
    public function assertNamedAttributeGroupExists(string $attributeGroupReference, string $name, string $langIso): void
    {
        $langId = (int) Language::getIdByIso($langIso);

        if (!$langId) {
            throw new RuntimeException(sprintf('Language by iso code "%s" was not found', $langIso));
        }

        $attributeGroups = LegacyAttributeGroup::getAttributesGroups($langId);
        $foundGroupId = null;

        foreach ($attributeGroups as $attributeGroup) {
            if ($attributeGroup['name'] === $name) {
                $foundGroupId = (int) $attributeGroup['id_attribute_group'];

                break;
            }
        }

        Assert::assertNotNull($foundGroupId, sprintf('Attribute group named "%s" was not found', $name));
        $this->getSharedStorage()->set($attributeGroupReference, $foundGroupId);
    }

    /**
     * @Given there is a list of following attribute groups:
     *
     * @param TableNode $tableNode
     */
    public function assertAllAttributeGroups(TableNode $tableNode): void
    {
        $attributeGroupsData = $this->localizeByColumns($tableNode);
        $attributeGroups = $this->getQueryBus()->handle(new GetAttributeGroupList(false));

        $this->assertAttributeGroups($attributeGroupsData, $attributeGroups);
    }

    /**
     * @Then product ":productReference" should have the following list of attribute groups:
     *
     * @param TableNode $tableNode
     */
    public function assertProductAttributeGroups(string $productReference, TableNode $tableNode): void
    {
        $attributeGroupsData = $this->localizeByColumns($tableNode);
        $productId = (int) $this->getSharedStorage()->get($productReference);
        $attributeGroups = $this->getQueryBus()->handle(new GetProductAttributeGroups($productId, false));

        $this->assertAttributeGroups($attributeGroupsData, $attributeGroups);
    }

    /**
     * @param array $attributeGroupsData
     * @param array $attributeGroups
     */
    private function assertAttributeGroups(array $attributeGroupsData, array $attributeGroups): void
    {
        Assert::assertEquals(count($attributeGroupsData), count($attributeGroups));
        foreach ($attributeGroupsData as $index => $attributeGroupsDatum) {
            /** @var AttributeGroup $attributeGroup */
            $attributeGroup = $attributeGroups[$index];
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
            Assert::assertNull($attributeGroup->getAttributes());

            $expectedId = $this->getSharedStorage()->get($attributeGroupsDatum['reference']);
            Assert::assertEquals($expectedId, $attributeGroup->getAttributeGroupId());
        }
    }

    /**
     * @Then product :productReference should have no attribute groups
     *
     * @param string $productReference
     */
    public function assertNoProductAttributes(string $productReference): void
    {
        $attributeGroups = $this->getQueryBus()->handle(new GetProductAttributeGroups(
            (int) $this->getSharedStorage()->get($productReference),
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
    public function assertAttributeInAllGroups(TableNode $tableNode, string $attributeGroupReference): void
    {
        $attributesData = $this->localizeByColumns($tableNode);
        $attributeGroups = $this->getQueryBus()->handle(new GetAttributeGroupList(true));

        $this->assertAttributesInGroup($attributesData, $attributeGroups, $attributeGroupReference);
    }

    /**
     * @Then product ":productReference" should have the following list of attributes in attribute group ":attributeGroupReference":
     *
     * @param TableNode $tableNode
     * @param string $productReference
     * @param string $attributeGroupReference
     */
    public function assertAttributeInProductGroups(TableNode $tableNode, string $productReference, string $attributeGroupReference): void
    {
        $attributesData = $this->localizeByColumns($tableNode);
        $attributeGroups = $this->getQueryBus()->handle(new GetProductAttributeGroups(
            (int) $this->getSharedStorage()->get($productReference),
            true
        ));

        $this->assertAttributesInGroup($attributesData, $attributeGroups, $attributeGroupReference);
    }

    /**
     * @param array $attributesData
     * @param array $attributeGroups
     * @param string $attributeGroupReference
     */
    private function assertAttributesInGroup(array $attributesData, array $attributeGroups, string $attributeGroupReference): void
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

        Assert::assertEquals(count($attributesData), count($checkAttributeGroup->getAttributes()));
        $attributes = $checkAttributeGroup->getAttributes();
        foreach ($attributesData as $index => $attributesDatum) {
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
