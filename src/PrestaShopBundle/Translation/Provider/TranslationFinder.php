<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Translation\Provider;

use InvalidArgumentException;
use PrestaShop\PrestaShop\Core\Exception\FileNotFoundException;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\Translation\Loader\XliffFileLoader;
use Symfony\Component\Translation\MessageCatalogue;
use Symfony\Component\Translation\MessageCatalogueInterface;

/**
 * Helper used to build a MessageCataloguer from xliff files
 *
 * @deprecated Please use PrestaShop\PrestaShop\Core\Translation\Provider\TranslationFinder instead
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
     * @throws FileNotFoundException
     */
    public function getCatalogueFromPaths(array $paths, string $locale, ?string $pattern = null): MessageCatalogue
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
     * @param string|null $pattern
     *
     * @return Finder
     *
     * @throws FileNotFoundException
     */
    private function getTranslationFilesFromPath(array $paths, ?string $pattern = null): Finder
    {
        $finder = new Finder();

        if (null !== $pattern) {
            $finder->name($pattern);
        }

        try {
            $translationFiles = $finder->files()->notName('index.php')->in($paths);
        } catch (InvalidArgumentException $e) {
            throw new FileNotFoundException(sprintf('Could not crawl for translation files: %s', $e->getMessage()), self::ERR_DIRECTORY_NOT_FOUND, $e);
        }

        if (count($translationFiles) === 0) {
            throw new FileNotFoundException('There are no translation file available.', self::ERR_NO_FILES_IN_DIRECTORY);
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
