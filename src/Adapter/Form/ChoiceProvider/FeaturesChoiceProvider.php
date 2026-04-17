<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Form\ChoiceProvider;

use PrestaShop\PrestaShop\Adapter\Feature\Repository\FeatureRepository;
use PrestaShop\PrestaShop\Adapter\LegacyContext;
use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use PrestaShop\PrestaShop\Core\Form\FormChoiceFormatter;
use PrestaShop\PrestaShop\Core\Form\FormChoiceProviderInterface;

class FeaturesChoiceProvider implements FormChoiceProviderInterface
{
    /**
     * Cache value to avoid performing the same request multiple times as the value should remain the same inside a request.
     *
     * @var array
     */
    private $cacheFeatureChoices;

    public function __construct(
        protected readonly FeatureRepository $featureRepository,
        protected readonly LegacyContext $legacyContext,
        protected readonly ConfigurationInterface $configuration
    ) {
    }

    /**
     * {@inheritDoc}
     */
    public function getChoices()
    {
        if (!empty($this->cacheFeatureChoices)) {
            return $this->cacheFeatureChoices;
        }

        $contextLangId = (int) $this->legacyContext->getLanguage()->getId();
        $contextShopId = (int) $this->legacyContext->getContext()->shop->id;

        $features = [];
        foreach ($this->featureRepository->getFeaturesByLang($contextLangId, $contextShopId) as $feature) {
            $features[] = [
                'id_feature' => $feature['id_feature'],
                'name' => $feature['localized_names'][$contextLangId],
            ];
        }

        return $this->cacheFeatureChoices = FormChoiceFormatter::formatFormChoices(
            $features,
            'id_feature',
            'name'
        );
    }
}
