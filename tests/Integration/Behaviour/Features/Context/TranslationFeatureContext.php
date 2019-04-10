<?php
/**
 * 2007-2019 PrestaShop and Contributors
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

namespace Tests\Integration\Behaviour\Features\Context;

use AppKernel;
use Context;

class TranslationFeatureContext extends AbstractPrestaShopFeatureContext
{
    /**
     * PrestaShop Symfony AppKernel
     *
     * Required to access services through the container
     *
     * @var AppKernel
     */
    protected static $kernel;

    /**
     * @BeforeSuite
     */
    public static function prepare($scope)
    {
        require_once __DIR__ . '/../../bootstrap.php';
        self::$kernel = new AppKernel('test', true);
        self::$kernel->boot();
    }

    /**
     * @Given there is a legacy translation for :translation in locale :locale in the module :module
     */
    public function iHaveALegacyTranslationForInLocaleInTheModule($translation, $locale, $module)
    {
        global $_MODULE;
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
     * @Then the module translated version of :key using domain :domain should be :value using locale :locale
     */
    public function theModuleTranslatedVersionOfUsingDomainShouldBeUsingLocale($key, $domain, $value, $locale)
    {
        $translatedValue = Context::getContext()->getTranslator()->trans($key, [], $domain, $locale);

        if ($translatedValue !== $value) {
            throw new \Exception(sprintf('The translation is wrong for key "%s": expected "%s" and retrieved "%s"',
               $key,
               $value,
               $translatedValue
            ));
        }
    }

    /**
     * @Then the Twig translated version of :key using domain :domain should be :value using locale :locale
     */
    public function theTwigTranslatedVersionOfUsingDomainShouldBeUsingLocale($key, $domain, $value, $locale)
    {
        $translator = $this::getContainer()->get('translator');
        $translatedValue = $translator->trans($key, [], $domain, 'fr-FR');

        if ($translatedValue !== $value) {
            throw new \Exception(sprintf('The translation is wrong for key "%s": expected "%s" and retrieved "%s"',
                $key,
                $value,
                $translatedValue
            ));
        }
    }

    /**
     * Return PrestaShop Symfony services container
     *
     * @return \Symfony\Component\DependencyInjection\ContainerInterface
     */
    public static function getContainer()
    {
        return static::$kernel->getContainer();
    }
}
