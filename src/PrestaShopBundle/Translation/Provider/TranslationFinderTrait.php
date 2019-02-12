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

namespace PrestaShopBundle\Translation\Provider;

use Symfony\Component\Finder\Finder;
use Symfony\Component\Translation\Loader\XliffFileLoader;
use Symfony\Component\Translation\MessageCatalogue;

/**
 * Helper used to retrieve a Symfony Catalogue object.
 */
trait TranslationFinderTrait
{
    /**
     * @param array $paths a list of paths when we can look for translations
     * @param string $locale the Symfony (not the PrestaShop one) locale
     * @param string|null $pattern a regular expression
     *
     * @return MessageCatalogue
     *
     * @throws \Exception
     */
    public function getCatalogueFromPaths($paths, $locale, $pattern = null)
    {
        $messageCatalogue = new MessageCatalogue($locale);
        $xliffFileLoader = new XliffFileLoader();
        $finder = new Finder();

        if (null !== $pattern) {
            $finder->name($pattern);
        }
        $translationFiles = $finder->files()->notName('index.php')->in($paths);

        if (count($translationFiles) === 0) {
            throw new \Exception('There is no translation file available.');
        }

        foreach ($translationFiles as $file) {
            if (strpos($file->getBasename('.xlf'), $locale) !== false) {
                $domain = $file->getBasename('.xlf');
            } else {
                $domain = $file->getBasename('.xlf') . '.' . $locale;
            }

            $fileCatalogue = $xliffFileLoader->load($file->getPathname(), $locale, $domain);
            $messageCatalogue->addCatalogue($fileCatalogue);
        }

        return $messageCatalogue;
    }
}
