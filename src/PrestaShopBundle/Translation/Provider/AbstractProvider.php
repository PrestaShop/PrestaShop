<?php
/**
 * 2007-2020 PrestaShop SA and Contributors
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
 * @copyright 2007-2020 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShopBundle\Translation\Provider;

use PrestaShop\PrestaShop\Core\Exception\FileNotFoundException;
use PrestaShop\PrestaShop\Core\Translation\Locale\Converter;
use Symfony\Component\Translation\Loader\LoaderInterface;
use Symfony\Component\Translation\MessageCatalogue;
use Symfony\Component\Translation\MessageCatalogueInterface;

abstract class AbstractProvider implements ProviderInterface
{
    const DEFAULT_LOCALE = 'en-US';

    /**
     * @var LoaderInterface Loader for database translations
     */
    private $databaseLoader;

    /**
     * @var string Path where translation files are found
     */
    protected $resourceDirectory;

    /**
     * @var string Locale to load the catalogue in
     */
    protected $locale;

    /**
     * @var string Catalogue domain
     */
    protected $domain;

    /**
     * @param LoaderInterface $databaseLoader
     * @param string $resourceDirectory Path where translations are found
     */
    public function __construct(LoaderInterface $databaseLoader, $resourceDirectory)
    {
        $this->databaseLoader = $databaseLoader;
        $this->resourceDirectory = $resourceDirectory;
        $this->locale = self::DEFAULT_LOCALE;
    }

    /**
     * Returns a list of directories to crawl for Xliff files
     *
     * @return string[]
     */
    protected function getDirectories()
    {
        return [$this->getResourceDirectory()];
    }

    /**
     * Returns a list of patterns used to filter a catalogue (including XLF file lookup) by translation domain.
     *
     * Only matching domains will be loaded by this provider.
     * Multiple filters are computed using OR.
     *
     * @return string[]
     */
    protected function getFilters()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getTranslationDomains()
    {
        return [''];
    }

    /**
     * {@inheritdoc}
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * {@inheritdoc}
     */
    public function setLocale($locale)
    {
        $this->locale = $locale;

        return $this;
    }

    /**
     * @param string $domain
     *
     * @return static
     */
    public function setDomain($domain)
    {
        $this->domain = $domain;

        return $this;
    }

    /**
     * Get the PrestaShop locale from real locale.
     *
     * @return string The PrestaShop locale
     *
     * @deprecated since 1.7.6, to be removed in the next major
     */
    public function getPrestaShopLocale()
    {
        @trigger_error(
            '`AbstractProvider::getPrestaShopLocale` function is deprecated and will be removed in the next major',
            E_USER_DEPRECATED
        );

        return Converter::toPrestaShopLocale($this->locale);
    }

    /**
     * {@inheritdoc}
     */
    public function getMessageCatalogue()
    {
        $messageCatalogue = $this->getDefaultCatalogue();

        // Merge catalogues

        $xlfCatalogue = $this->getXliffCatalogue();
        $messageCatalogue->addCatalogue($xlfCatalogue);
        unset($xlfCatalogue);

        $databaseCatalogue = $this->getDatabaseCatalogue();
        $messageCatalogue->addCatalogue($databaseCatalogue);
        unset($databaseCatalogue);

        return $messageCatalogue;
    }

    /**
     * {@inheritdoc}
     *
     * @throws FileNotFoundException
     */
    public function getDefaultCatalogue($empty = true)
    {
        $defaultCatalogue = new MessageCatalogue($this->locale);

        foreach ($this->getFilters() as $filter) {
            $filteredCatalogue = $this->getCatalogueFromPaths(
                [$this->getDefaultResourceDirectory()],
                $this->locale,
                $filter
            );
            $defaultCatalogue->addCatalogue($filteredCatalogue);
        }

        if ($empty && $this->locale !== self::DEFAULT_LOCALE) {
            $defaultCatalogue = $this->emptyCatalogue($defaultCatalogue);
        }

        return $defaultCatalogue;
    }

    /**
     * {@inheritdoc}
     */
    public function getXliffCatalogue()
    {
        $xlfCatalogue = new MessageCatalogue($this->locale);

        foreach ($this->getFilters() as $filter) {
            try {
                $filteredCatalogue = $this->getCatalogueFromPaths(
                    $this->getDirectories(),
                    $this->locale,
                    $filter
                );
                $xlfCatalogue->addCatalogue($filteredCatalogue);
            } catch (FileNotFoundException $e) {
                // there are no translation files, ignore them
            }
        }

        return $xlfCatalogue;
    }

    /**
     * {@intheritdoc}
     */
    public function getDatabaseCatalogue($theme = null)
    {
        $databaseCatalogue = new MessageCatalogue($this->locale);

        foreach ($this->getTranslationDomains() as $translationDomain) {
            $domainCatalogue = $this->getDatabaseLoader()->load(null, $this->locale, $translationDomain, $theme);

            if ($domainCatalogue instanceof MessageCatalogue) {
                $databaseCatalogue->addCatalogue($domainCatalogue);
            }
        }

        return $databaseCatalogue;
    }

    /**
     * Returns the directory where translation files for the current locale are
     *
     * @return string
     */
    public function getResourceDirectory()
    {
        return $this->resourceDirectory . DIRECTORY_SEPARATOR . $this->locale;
    }

    /**
     * Returns the loader for database translations
     *
     * @return LoaderInterface
     */
    public function getDatabaseLoader()
    {
        return $this->databaseLoader;
    }

    /**
     * Empties out the catalogue by removing translations but leaving keys
     *
     * @param MessageCatalogueInterface $messageCatalogue
     *
     * @return MessageCatalogueInterface Empty the catalogue
     */
    public function emptyCatalogue(MessageCatalogueInterface $messageCatalogue)
    {
        foreach ($messageCatalogue->all() as $domain => $messages) {
            foreach (array_keys($messages) as $translationKey) {
                $messageCatalogue->set($translationKey, '', $domain);
            }
        }

        return $messageCatalogue;
    }

    /**
     * Loads the catalogue from the provided paths
     *
     * @param string|string[] $paths a list of paths when we can look for translations
     * @param string $locale the Symfony (not the PrestaShop one) locale
     * @param string|null $pattern a regular expression
     *
     * @return MessageCatalogue
     *
     * @throws FileNotFoundException
     */
    public function getCatalogueFromPaths($paths, $locale, $pattern = null)
    {
        return (new TranslationFinder())->getCatalogueFromPaths($paths, $locale, $pattern);
    }

    /**
     * Returns the path to the directory where the default (aka not translated) catalogue is
     *
     * @return string
     */
    abstract public function getDefaultResourceDirectory();
}
