<?php
/**
 * 2007-2019 PrestaShop
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
 * @copyright 2007-2019 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace Tests\Integration\Behaviour\Features\Context;

use Behat\Behat\Tester\Exception\PendingException;
use Context;

class TranslationFeatureContext extends AbstractPrestaShopFeatureContext
{
    /**
     * @Given I have a legacy translation for :translation in locale :locale in the module :module
     */
    public function iHaveALegacyTranslationForInLocaleInTheModule($translation, $locale, $module)
    {
        $translationFile = self::MODULES_DIRECTORY . '/' . $module . '/translations/' . $locale . '.php';

        if (file_exists($translationFile)) {
            require $translationFile;

            $translationkey = '#' . md5($translation) . '#';
            foreach ($_MODULE as $key => $value) {
                if (preg_match($translationkey, $key)) {
                    return true;
                }
            }
        }

        throw new \Exception(sprintf('The translation %s doesnt exists', $translation));
    }

    /**
     * @Then the Module translated version of :key using domain :domain should be :value
     */
    public function theModuleTranslatedVersionOfUsingDomainShouldBe($key, $domain, $value)
    {
        $translatedValue = Context::getContext()->getTranslator()->trans($key, [], $domain);
        if ($translatedValue !== $value) {
            throw new \Exception(sprintf('The translation is wrong for key "%s": expected "%s" and retrieved "%s"',
               $key,
               $value,
               $translatedValue
            ));
        }
    }

    /**
     * @Then the Twig translated version of :key using domain :domain should be :value
     */
    public function theTwigTranslatedVersionOfUsingDomainShouldBe($key, $domain, $value)
    {
        throw new PendingException();
    }
}
