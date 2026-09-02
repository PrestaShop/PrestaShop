<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Tests\Integration\Behaviour\Features\Context;

use AttributeGroup;
use Language;
use PHPUnit\Framework\Assert;
use RuntimeException;
use Tests\Integration\Behaviour\Features\Context\Domain\AbstractDomainFeatureContext;

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

        $attributeGroups = AttributeGroup::getAttributesGroups($langId);
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
     * @Given /^I associate attribute group "(.+)" with shops "(.+)"$/
     *
     * @param string $attributeGroupReference
     * @param string $shopReferences
     *
     * @return void
     */
    public function associateAttributeGroupWithShops(string $attributeGroupReference, string $shopReferences): void
    {
        $attributeGroupId = $this->getSharedStorage()->get($attributeGroupReference);
        $attributeGroup = new AttributeGroup($attributeGroupId);

        if ($attributeGroupId !== (int) $attributeGroup->id) {
            throw new RuntimeException(
                sprintf(
                    'Failed to load Attribute group with id %d. Referenced as "%s"',
                    $attributeGroupId,
                    $attributeGroupReference
                )
            );
        }
        $attributeGroup->associateTo($this->referencesToIds($shopReferences));
    }

    /**
     * @Given attribute group ":attributeGroupReference" is not associated to shops ":shopReferences"
     *
     * @param string $attributeGroupReference
     * @param string $shopReferences
     *
     * @return void
     */
    public function assertAttributeGroupIsNotAssociatedToShops(string $attributeGroupReference, string $shopReferences): void
    {
        $attributeGroupId = $this->getSharedStorage()->get($attributeGroupReference);
        $attributeGroup = new AttributeGroup($attributeGroupId);
        $shopIds = $this->referencesToIds($shopReferences);

        foreach ($shopIds as $shopId) {
            if (in_array($shopId, $attributeGroup->id_shop_list)) {
                throw new RuntimeException(
                    sprintf(
                        'Attribute group with id "%d" is associated with shop "%d"',
                        $attributeGroupId,
                        $shopId
                    )
                );
            }
        }
    }
}
