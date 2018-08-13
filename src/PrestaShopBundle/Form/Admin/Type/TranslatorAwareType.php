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

namespace PrestaShopBundle\Form\Admin\Type;

use PrestaShop\PrestaShop\Adapter\Language\ContextLanguageDataProvider;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * PrestaShop forms needs custom domain name for field constraints
 * This feature is not available in Symfony so we need to inject the translator
 * for constraints messages only.
 */
abstract class TranslatorAwareType extends CommonAbstractType
{
    private $translator;

    /**
     * Active languages available on shop.
     *
     * @param array $locales
     */
    protected $locales;

    /**
     * Active and in-active languages available on shop. Used to apply translations
     *
     * @var array $allLocales
     */
    protected $allLocales;

    public function __construct(
        TranslatorInterface $translator,
        ContextLanguageDataProvider $languageDataProvider
    ) {
        $this->translator = $translator;
        $this->locales = $languageDataProvider->getActiveLocales();
        $this->allLocales = $languageDataProvider->getIncludingInactiveLocales();
    }

    /**
     * Get the translated chain from key
     *
     * @param $key - the key to be translated
     * @param $domain - the domain to be selected
     * @param array $parameters Optional, pass parameters if needed (uncommon)
     *
     * @return string
     */
    protected function trans($key, $domain, $parameters = [])
    {
        return $this->translator->trans($key, $parameters, $domain);
    }

    /**
     * Get locales to be used in form type
     *
     * @return array
     */
    protected function getLocaleChoices()
    {
        return $this->formatLocales($this->locales);
    }

    /**
     * Get locales to be used in form type including the disabled ones
     *
     * @return array
     */
    protected function getIncludingInactiveLocalesChoices()
    {
        return $this->formatLocales($this->allLocales);
    }

    /**
     * Formats the response so the array key becomes the name of locale and value is the iso code of the locale.
     *
     * @param array $locales
     *
     * @return array
     */
    private function formatLocales(array $locales)
    {
        $result = [];
        foreach ($locales as $locale) {
            $result[$locale['name']] = $locale['iso_code'];
        }

        return $result;
    }
}
