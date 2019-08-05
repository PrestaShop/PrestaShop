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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShopBundle\Translation\Provider;

use PrestaShop\PrestaShop\Core\Exception\FileNotFoundException;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\Translation\Loader\XliffFileLoader;
use Symfony\Component\Translation\MessageCatalogue;
use Symfony\Component\Translation\MessageCatalogueInterface;

/**
 * Helper used to build a MessageCataloguer from xliff files
 */
class TranslationFinder
{
    const ERR_NO_FILES_IN_DIRECTORY = 1;
    const ERR_DIRECTORY_NOT_FOUND = 2;

    /**
     * @param string|array $paths a list of paths when we can look for translations
     * @param string $locale the Symfony (not the PrestaShop one) locale
     * @param string|null $pattern a regular expression
     *
     * @return MessageCatalogue
     *
     * @throws FileNotFoundException
     */
    public function getCatalogueFromPaths($paths, $locale, $pattern = null)
    {
        $translationFiles = $this->getTranslationFilesFromPath($paths, $pattern);

        return $this->buildCatalogueFromFiles($translationFiles, $locale);
    }

    /**
     * @param MessageCatalogueInterface $catalogue
     *
     * @return MessageCatalogue
     */
    private function removeTrailingLocaleFromDomains(MessageCatalogueInterface $catalogue)
    {
        $messages = $catalogue->all();
        $locale = $catalogue->getLocale();
        $localeSuffix = '.' . $locale;
        $suffixLength = strlen($localeSuffix);

        foreach ($catalogue->getDomains() as $domain) {
            if (substr($domain, -$suffixLength) === $localeSuffix) {
                $cleanDomain = substr($domain, 0, -$suffixLength);
                $messages[$cleanDomain] = $messages[$domain];
                unset($messages[$domain]);
            }
        }

        return new MessageCatalogue($locale, $messages);
    }

    /**
     * @param $paths
     * @param $pattern
     *
     * @return Finder
     *
     * @throws FileNotFoundException
     */
    private function getTranslationFilesFromPath($paths, $pattern)
    {
        $finder = new Finder();

        if (null !== $pattern) {
            $finder->name($pattern);
        }

        try {
            $translationFiles = $finder->files()->notName('index.php')->in($paths);
        } catch (\InvalidArgumentException $e) {
            throw new FileNotFoundException(
                sprintf(
                    'Could not crawl for translation files: %s',
                    $e->getMessage()
                ),
                self::ERR_DIRECTORY_NOT_FOUND,
                $e
            );
        }

        if (count($translationFiles) === 0) {
            throw new FileNotFoundException(
                'There are no translation file available.', self::ERR_NO_FILES_IN_DIRECTORY
            );
        }

        return $translationFiles;
    }

    /**
     * @param Finder $translationFiles
     * @param string $locale
     *
     * @return MessageCatalogue
     */
    private function buildCatalogueFromFiles(Finder $translationFiles, $locale)
    {
        $messageCatalogue = new MessageCatalogue($locale);
        $xliffFileLoader = new XliffFileLoader();

        /** @var SplFileInfo $file */
        foreach ($translationFiles as $file) {
            if ('xlf' === $file->getExtension()) {
                $domain = $this->getDomainFromFile($file, $locale);

                $fileCatalogue = $xliffFileLoader->load($file->getPathname(), $locale, $domain);
                $messageCatalogue->addCatalogue(
                    $this->removeTrailingLocaleFromDomains($fileCatalogue)
                );
            }
        }

        return $messageCatalogue;
    }

    /**
     * @param SplFileInfo $file
     * @param string $locale
     *
     * @return string
     */
    private function getDomainFromFile(SplFileInfo $file, $locale)
    {
        $basename = $file->getBasename('.xlf');

        $domain = $basename;
        if (strpos($basename, $locale) === false) {
            $domain .= '.' . $locale;
        }

        return $domain;
    }
}
