<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Behaviour\Features\Context;

use Language;
use PHPUnit\Framework\Assert;
use ProductAttribute;
use RuntimeException;
use Tests\Integration\Behaviour\Features\Context\Domain\AbstractDomainFeatureContext;

class AttributeFeatureContext extends AbstractDomainFeatureContext
{
    /**
     * @Given attribute :attributeReference named :name in :langIso language exists
     *
     * @param string $attributeReference
     * @param string $name
     * @param string $langIso
     */
    public function assertNamedAttributeExists(string $attributeReference, string $name, string $langIso): void
    {
        $langId = (int) Language::getIdByIso($langIso);

        if (!$langId) {
            throw new RuntimeException(sprintf('Language by iso code "%s" was not found', $langIso));
        }

        $attributes = ProductAttribute::getAttributes($langId);
        $foundAttributeId = null;

        foreach ($attributes as $attribute) {
            if ($attribute['name'] === $name) {
                $foundAttributeId = (int) $attribute['id_attribute'];

                break;
            }
        }

        Assert::assertNotNull($foundAttributeId, sprintf('Attribute named "%s" was not found', $name));
        $this->getSharedStorage()->set($attributeReference, $foundAttributeId);
    }

    /**
     * @Given /^I associate attribute "(.+)" with shops "(.+)"$/
     *
     * @param string $attributeReference
     * @param string $shopReferences
     *
     * @return void
     */
    public function associateAttributeWithShops(string $attributeReference, string $shopReferences): void
    {
        $attributeId = $this->getSharedStorage()->get($attributeReference);
        $attribute = new ProductAttribute($attributeId);

        if ($attributeId !== (int) $attribute->id) {
            throw new RuntimeException(
                sprintf(
                    'Failed to load Attribute with id %d. Referenced as "%s"',
                    $attributeId,
                    $attributeReference
                )
            );
        }
        $attribute->associateTo($this->referencesToIds($shopReferences));
    }

    /**
     * @Given /^I switch positions of attributes "(.+)" and "(.+)"$/
     *
     * @param string $attributeReference
     * @param string $secondAttributeReference
     *
     * @return void
     */
    public function switchAttributePosition(string $attributeReference, string $secondAttributeReference): void
    {
        $attributeId = $this->getSharedStorage()->get($attributeReference);
        $secondAttributeId = $this->getSharedStorage()->get($secondAttributeReference);
        $attribute = new ProductAttribute($attributeId);
        $secondAttribute = new ProductAttribute($secondAttributeId);

        $position = (int) $attribute->position;
        $attribute->position = $secondAttribute->position;
        $secondAttribute->position = $position;
        $attribute->save();
        $secondAttribute->save();
    }

    /**
     * @Given attribute ":attributeReference" is not associated to shops ":shopReferences"
     *
     * @param string $attributeReference
     * @param string $shopReferences
     *
     * @return void
     */
    public function assertAttributeIsNotAssociatedToShops(string $attributeReference, string $shopReferences): void
    {
        $attributeId = $this->getSharedStorage()->get($attributeReference);
        $attribute = new ProductAttribute($attributeId);
        $shopIds = $this->referencesToIds($shopReferences);

        foreach ($shopIds as $shopId) {
            if (in_array($shopId, $attribute->id_shop_list)) {
                throw new RuntimeException(
                    sprintf(
                        'Attribute with id "%d" is associated with shop "%d"',
                        $attributeId,
                        $shopId
                    )
                );
            }
        }
    }
}
