<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Form\ChoiceProvider;

use PrestaShop\PrestaShop\Adapter\Feature\Repository\FeatureValueRepository;
use PrestaShop\PrestaShop\Adapter\LegacyContext;
use PrestaShop\PrestaShop\Core\Form\ConfigurableFormChoiceProviderInterface;
use PrestaShop\PrestaShop\Core\Form\FormChoiceFormatter;

class FeatureValuesChoiceProvider implements ConfigurableFormChoiceProviderInterface
{
    /**
     * @var FeatureValueRepository
     */
    private $featureValueRepository;

    /**
     * @var int
     */
    private $contextLanguageId;

    /**
     * Cache value to avoid performing the same request multiple times as the value should remain the same inside a request.
     *
     * @var array
     */
    private $cacheFeatureValueChoices;

    public function __construct(
        FeatureValueRepository $featureValueRepository,
        LegacyContext $legacyContext
    ) {
        $this->featureValueRepository = $featureValueRepository;
        $this->contextLanguageId = (int) $legacyContext->getLanguage()->getId();
    }

    /**
     * {@inheritDoc}
     */
    public function getChoices(array $options)
    {
        if (empty($options['feature_id'])) {
            return [];
        }

        $filters = [
            'id_feature' => (int) $options['feature_id'],
        ];
        if (isset($options['custom'])) {
            $filters['custom'] = $options['custom'];
        }

        // Get cache key and if this is the first time we are accessing it,
        // we build the options
        $cacheKey = md5(serialize($filters));
        if (empty($this->cacheFeatureValueChoices[$cacheKey])) {
            $this->cacheFeatureValueChoices[$cacheKey] = FormChoiceFormatter::formatFormChoices(
                $this->featureValueRepository->getFeatureValuesByLang($this->contextLanguageId, $filters),
                'id_feature_value',
                'value'
            );
        }

        return $this->cacheFeatureValueChoices[$cacheKey];
    }
}
