<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Entity\Repository;

use PrestaShop\PrestaShop\Adapter\Attribute\Repository\AttributeRepository;
use PrestaShop\PrestaShop\Adapter\Feature\Repository\FeatureRepository;
use PrestaShop\PrestaShop\Core\Context\LanguageContext;
use PrestaShop\PrestaShop\Core\Context\ShopContext;

/**
 * FeatureAttributeRepository
 *
 * @deprecated since 9.1 and will be removed in 10.0, this repository don't have to be used anymore. Use instead FeaturesRepository or AttributesRepository directly.
 */
class FeatureAttributeRepository
{
    use NormalizeFieldTrait;

    /**
     * FeatureAttributeRepository constructor.
     */
    public function __construct(
        private readonly ShopContext $shopContext,
        private readonly LanguageContext $languageContext,
        private readonly FeatureRepository $featureRepository,
        private readonly AttributeRepository $attributeRepository
    ) {
    }

    /**
     * @return mixed
     */
    public function getAttributes()
    {
        return $this->attributeRepository->getAttributesWithValues($this->languageContext->getId(), $this->shopContext->getAssociatedShopIds());
    }

    /**
     * @return mixed
     */
    public function getFeatures()
    {
        return $this->featureRepository->getFeaturesWithValues($this->languageContext->getId(), $this->shopContext->getAssociatedShopIds());
    }
}
