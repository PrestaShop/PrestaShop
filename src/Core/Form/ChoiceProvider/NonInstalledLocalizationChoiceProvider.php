<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Form\ChoiceProvider;

use PrestaShop\PrestaShop\Adapter\Language\LanguageDataProvider;
use PrestaShop\PrestaShop\Core\Form\FormChoiceProviderInterface;
use PrestaShop\PrestaShop\Core\Language\LanguageValidatorInterface;

/**
 * Class NonInstalledLocalizationChoiceProvider provides non installed localization choices
 * with name keys and iso code values.
 */
final class NonInstalledLocalizationChoiceProvider implements FormChoiceProviderInterface
{
    /**
     * @var LanguageValidatorInterface
     */
    private $languageValidator;

    /**
     * @var LanguageDataProvider
     */
    private $languageProvider;
    /**
     * @var array
     */
    private $languagePackList;

    /**
     * @param array $languagePackList
     * @param LanguageValidatorInterface $languageValidator
     * @param LanguageDataProvider $languageProvider
     */
    public function __construct(
        array $languagePackList,
        LanguageValidatorInterface $languageValidator,
        LanguageDataProvider $languageProvider
    ) {
        $this->languageValidator = $languageValidator;
        $this->languageProvider = $languageProvider;
        $this->languagePackList = $languagePackList;
    }

    /**
     * {@inheritdoc}
     */
    public function getChoices()
    {
        $choices = [];
        foreach (array_keys($this->languagePackList) as $locale) {
            if ($this->languageValidator->isInstalledByLocale($locale)) {
                continue;
            }

            $languageDetails = $this->languageProvider->getLanguageDetails($locale);

            if (isset($languageDetails['iso_code'], $languageDetails['name'])) {
                $choices[$languageDetails['name']] = $languageDetails['iso_code'];
            }
        }

        return $choices;
    }
}
