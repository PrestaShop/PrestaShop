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

use PrestaShop\PrestaShop\Adapter\Feature\Repository\FeatureRepository;
use PrestaShop\PrestaShop\Adapter\LegacyContext;
use PrestaShop\PrestaShop\Core\Form\FormChoiceProviderInterface;

class FeaturesChoiceProvider implements FormChoiceProviderInterface
{
    /**
     * @var FeatureRepository
     */
    private $featureRepository;

    /**
     * @var int
     */
    private $contextLanguageId;

    /**
     * Cache value to avoid performing the same request multiple times as the value should remain the same inside a request.
     *
     * @var array
     */
    private $cacheFeatureChoices;

    public function __construct(
        FeatureRepository $featureRepository,
        LegacyContext $legacyContext
    ) {
        $this->featureRepository = $featureRepository;
        $this->contextLanguageId = (int) $legacyContext->getLanguage()->getId();
    }

    /**
     * {@inheritDoc}
     */
    public function getChoices()
    {
        if (!empty($this->cacheFeatureChoices)) {
            return $this->cacheFeatureChoices;
        }

        $features = $this->featureRepository->getFeaturesByLang($this->contextLanguageId);
        $this->cacheFeatureChoices = [];
        foreach ($features as $feature) {
            $this->cacheFeatureChoices[$feature['localized_names'][$this->contextLanguageId]] = $feature['id_feature'];
        }

        return $this->cacheFeatureChoices;
    }
}
