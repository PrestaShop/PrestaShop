<?php
/**
 * 2007-2018 PrestaShop
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Core\Form\ChoiceProvider;

use PrestaShop\PrestaShop\Adapter\Language\LanguageDataProvider;
use PrestaShop\PrestaShop\Adapter\Language\LanguageValidator;
use PrestaShop\PrestaShop\Core\Form\FormChoiceProviderInterface;
use PrestaShop\PrestaShop\Core\Language\Pack\Loader\LanguagePackLoaderInterface;

/**
 * Class NonInstalledLocalizationChoiceProvider is responsible for getting one part of choices to use
 * in 'Improve > International > Translations' page Add / Update a language form type.
 */
class NonInstalledLocalizationChoiceProvider implements FormChoiceProviderInterface
{
    /**
     * @var LanguageValidator
     */
    private $languageValidator;
    /**
     * @var LanguagePackLoaderInterface
     */
    private $languagePackLoader;
    /**
     * @var LanguageDataProvider
     */
    private $languageProvider;

    public function __construct(
        LanguagePackLoaderInterface $languagePackLoader,
        LanguageValidator $languageValidator,
        LanguageDataProvider $languageProvider
    ) {
        $this->languageValidator = $languageValidator;
        $this->languagePackLoader = $languagePackLoader;
        $this->languageProvider = $languageProvider;
    }

    public function getChoices()
    {
        $languages = $this->languagePackLoader->getLanguagePackList();
        $choices = [];
        if (!empty($languages)) {
            foreach ($languages as $locale => $name) {
                if ($this->languageValidator->isInstalledByLocale($locale)) {
                    continue;
                }

                $languageDetails = $this->languageProvider->getJsonLanguageDetails($locale);

                if (isset($languageDetails['iso_code'], $languageDetails['name'])) {
                    $choices[$languageDetails['name']] = $languageDetails['iso_code'];
                }
            }
        }

        return $choices;
    }
}
