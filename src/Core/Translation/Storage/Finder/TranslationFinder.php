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
        $suffixLength = strlen($localeSuffix) * -1;

        foreach ($catalogue->getDomains() as $domain) {
            if (substr($domain, $suffixLength) === $localeSuffix) {
                $cleanDomain = substr($domain, 0, $suffixLength);
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

                $messageCatalogue = $this->updateCatalogueMetadata($messageCatalogue, $fileCatalogue);
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
        if (strpos($basename, $locale) === false) {
            $domain .= '.' . $locale;
        }

        return $domain;
    }

    /**
     * Get metadata from original catalogue, parse them to extract filename and line and add them to the updated catalogue
     *
     * @param MessageCatalogue $catalogue
     * @param MessageCatalogue $originalCatalogue
     *
     * @return MessageCatalogue
     */
    private function updateCatalogueMetadata(MessageCatalogue $catalogue, MessageCatalogue $originalCatalogue): MessageCatalogue
    {
        $locale = $originalCatalogue->getLocale();
        $localeSuffix = '.' . $locale;
        $suffixLength = strlen($localeSuffix);

        foreach ($originalCatalogue->all() as $domain => $messages) {
            $originalDomain = $domain;
            // Remove locale suffix from domain name
            if (substr($domain, -$suffixLength) === $localeSuffix) {
                $domain = substr($domain, 0, -$suffixLength);
            }
            foreach (array_keys($messages) as $translationKey) {
                $metadata = $originalCatalogue->getMetadata($translationKey, $originalDomain);
                if ($this->shouldAddFileMetadata($metadata) && $this->metadataContainNotes($metadata)) {
                    $catalogue->setMetadata($translationKey, $this->parseMetadataNotes($metadata), $domain);
                }
            }
        }

        return $catalogue;
    }

    /**
     * @param array|null $metadata
     *
     * @return bool
     */
    private function metadataContainNotes(array $metadata = null): bool
    {
        return is_array($metadata)
            && array_key_exists('notes', $metadata)
            && is_array($metadata['notes'])
            && array_key_exists(0, $metadata['notes'])
            && is_array($metadata['notes'][0])
            && array_key_exists('content', $metadata['notes'][0]);
    }

    /**
     * @param array|null $metadata
     *
     * @return bool
     */
    private function shouldAddFileMetadata(array $metadata = null): bool
    {
        return null === $metadata || !array_key_exists('file', $metadata) || !array_key_exists('line', $metadata);
    }

    /**
     * @param array|null $metadata
     *
     * @return array
     */
    private function parseMetadataNotes(array $metadata = null): array
    {
        $defaultMetadata = ['file' => '', 'line' => ''];

        if (!isset($metadata['file']['original'])) {
            return $defaultMetadata;
        }

        $notes = $metadata['notes'][0]['content'];
        if (1 !== preg_match('/Line: (?<line>\d+)/', $notes, $matches)) {
            return $defaultMetadata;
        }

        return [
            'file' => $metadata['file']['original'],
            'line' => (int) $matches['line'],
        ];
    }
}
