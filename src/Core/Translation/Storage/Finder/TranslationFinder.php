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

namespace PrestaShop\PrestaShop\Core\Translation\Storage\Finder;

use InvalidArgumentException;
use PrestaShop\PrestaShop\Core\Translation\Exception\TranslationFilesNotFoundException;
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
    private const ERR_NO_FILES_IN_DIRECTORY = 1;
    private const ERR_DIRECTORY_NOT_FOUND = 2;

    /**
     * @param array $paths a list of paths when we can look for translations
     * @param string $locale the Symfony (not the PrestaShop one) locale
     * @param string|null $pattern a regular expression
     *
     * @return MessageCatalogue
     *
     * @throws TranslationFilesNotFoundException
     */
    public function getCatalogueFromPaths(array $paths, string $locale, string $pattern = null): MessageCatalogue
    {
        $translationFiles = $this->getTranslationFilesFromPath($paths, $pattern);

        return $this->buildCatalogueFromFiles($translationFiles, $locale);
    }

    /**
     * @param MessageCatalogueInterface $catalogue
     *
     * @return MessageCatalogue
     */
    private function removeTrailingLocaleFromDomains(MessageCatalogueInterface $catalogue): MessageCatalogue
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
     * @param string[] $paths
     * @param string $pattern
     *
     * @return Finder
     *
     * @throws TranslationFilesNotFoundException
     */
    private function getTranslationFilesFromPath(array $paths, ?string $pattern): Finder
    {
        $finder = new Finder();

        if (null !== $pattern) {
            $finder->name($pattern);
        }

        try {
            $translationFiles = $finder->files()->notName('index.php')->in($paths);
        } catch (InvalidArgumentException $e) {
            throw new TranslationFilesNotFoundException(sprintf('Could not crawl for translation files: %s', $e->getMessage()), self::ERR_DIRECTORY_NOT_FOUND, $e);
        }

        if (count($translationFiles) === 0) {
            throw new TranslationFilesNotFoundException('There are no translation file available.', self::ERR_NO_FILES_IN_DIRECTORY);
        }

        return $translationFiles;
    }

    /**
     * @param Finder $translationFiles
     * @param string $locale
     *
     * @return MessageCatalogue
     */
    private function buildCatalogueFromFiles(Finder $translationFiles, string $locale): MessageCatalogue
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
    private function getDomainFromFile(SplFileInfo $file, string $locale): string
    {
        $basename = $file->getBasename('.xlf');

        $domain = $basename;
        if (!str_contains($basename, $locale)) {
            $domain .= '.' . $locale;
        }

        return $domain;
    }
}
