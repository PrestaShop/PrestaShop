<?php
/**
 * 2007-2019 PrestaShop SA and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Adapter\Currency\CommandHandler;

use Currency;
use PrestaShop\PrestaShop\Adapter\Domain\AbstractObjectModelHandler;
use PrestaShop\PrestaShop\Core\Domain\Language\Exception\LanguageNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Language\ValueObject\LanguageId;
use PrestaShop\PrestaShop\Core\Language\LanguageInterface;
use PrestaShop\PrestaShop\Core\Localization\CLDR\LocaleRepository;
use PrestaShop\PrestaShop\Core\Localization\Currency\PatternTransformer;

/**
 * Class AbstractCurrencyHandler is responsible for encapsulating common behavior for legacy currency object model.
 *
 * @internal
 */
abstract class AbstractCurrencyHandler extends AbstractObjectModelHandler
{
    /**
     * @var LocaleRepository
     */
    protected $localeRepoCLDR;

    /**
     * @var LanguageInterface[]
     */
    protected $languages;

    /**
     * @param LocaleRepository $localeRepoCLDR
     * @param LanguageInterface[] $languages
     */
    public function __construct(
        LocaleRepository $localeRepoCLDR,
        array $languages
    ) {
        $this->localeRepoCLDR = $localeRepoCLDR;
        $this->languages = $languages;
    }

    /**
     * Associations conversion rate to given shop ids.
     *
     * @param Currency $entity
     * @param array $shopIds
     */
    protected function associateConversionRateToShops(Currency $entity, array $shopIds)
    {
        $columnsToUpdate = [];
        foreach ($shopIds as $shopId) {
            $columnsToUpdate[$shopId] = [
                'conversion_rate' => $entity->conversion_rate,
            ];
        }

        $this->updateMultiStoreColumns($entity, $columnsToUpdate);
    }

    /**
     * @param Currency $entity
     *
     * @throws \PrestaShopDatabaseException
     * @throws \PrestaShopException
     * @throws \PrestaShop\PrestaShop\Core\Localization\Exception\LocalizationException
     */
    protected function refreshLocalizedData(Currency $entity)
    {
        $languagesData = [];
        foreach ($this->languages as $language) {
            $languagesData[] = [
                'id_lang' => $language->getId(),
            ];
        }

        //This method will insert the missing localized names/symbols and detect if the currency has been modified
        $entity->refreshLocalizedCurrencyData($languagesData, $this->localeRepoCLDR);
    }

    /**
     * @param Currency $entity
     * @param array $localizedTransformations
     *
     * @throws LanguageNotFoundException
     */
    protected function applyPatternTransformations(Currency $entity, array $localizedTransformations)
    {
        $transformer = new PatternTransformer();
        $localizedPatterns = [];
        foreach ($localizedTransformations as $langId => $transformationType) {
            if (empty($transformationType)) {
                continue;
            }

            $languageCurrencyPattern = $this->getCurrencyPatternByLanguageId($langId);
            $localizedPatterns[$langId] = $transformer->transform($languageCurrencyPattern, $transformationType);
        }
        $entity->setLocalizedPatterns($localizedPatterns);
    }

    /**
     * @param int $langId
     *
     * @return string
     *
     * @throws LanguageNotFoundException
     */
    private function getCurrencyPatternByLanguageId(int $langId)
    {
        /** @var LanguageInterface $language */
        foreach ($this->languages as $language) {
            if ($langId === $language->getId()) {
                return $this->localeRepoCLDR->getLocale($language->getLocale())->getCurrencyPattern();
            }
        }

        throw new LanguageNotFoundException(new LanguageId($langId));
    }
}
