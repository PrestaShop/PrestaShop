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

namespace PrestaShop\PrestaShop\Adapter\Form\ChoiceProvider;

use PrestaShop\PrestaShop\Adapter\Feature\Repository\FeatureValueRepository;
use PrestaShop\PrestaShop\Adapter\LegacyContext;
use PrestaShop\PrestaShop\Core\Form\ConfigurableFormChoiceProviderInterface;

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
     * @var int
     */
    private $defaultLanguageId;

    /**
     * Cache value to avoid performing the same request multiple times as the value should remain the same inside a request.
     *
     * @var array
     */
    private $cacheFeatureValueChoices;

    public function __construct(
        FeatureValueRepository $featureValueRepository,
        LegacyContext $legacyContext,
        int $defaultLanguageId
    ) {
        $this->featureValueRepository = $featureValueRepository;
        $this->contextLanguageId = (int) $legacyContext->getLanguage()->getId();
        $this->defaultLanguageId = $defaultLanguageId;
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
        $filters['id_lang'] = [
            $this->contextLanguageId,
            $this->defaultLanguageId,
        ];
        $cacheKey = md5(serialize($filters));

        if (!empty($this->cacheFeatureValueChoices[$cacheKey])) {
            return $this->cacheFeatureValueChoices[$cacheKey];
        }

        $featureValues = $this->featureValueRepository->getFeatureValues(null, null, $filters);
        $this->cacheFeatureValueChoices[$cacheKey] = [];
        foreach ($featureValues as $feature) {
            if (!empty($feature['localized_values'][$this->contextLanguageId])) {
                $featureValueName = $feature['localized_values'][$this->contextLanguageId];
            } elseif (!empty($feature['localized_values'][$this->defaultLanguageId])) {
                $featureValueName = $feature['localized_values'][$this->defaultLanguageId];
            } else {
                $featureValueName = reset($feature['localized_values']);
            }
            $this->cacheFeatureValueChoices[$cacheKey][$featureValueName] = (int) $feature['id_feature_value'];
        }

        // Order alphabetically
        ksort($this->cacheFeatureValueChoices[$cacheKey]);

        return $this->cacheFeatureValueChoices[$cacheKey];
    }
}
