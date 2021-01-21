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

namespace PrestaShopBundle\Translation\Provider;

use PrestaShop\PrestaShop\Core\Exception\FileNotFoundException;
use PrestaShop\PrestaShop\Core\Translation\Locale\Converter;
use PrestaShopBundle\Translation\Loader\DatabaseTranslationLoader;
use Symfony\Component\Translation\MessageCatalogue;

/**
 * Able to search translations for a specific translation domains across multiple sources
 */
class SearchProvider implements ProviderInterface, UseModuleInterface
{
    public const DEFAULT_LOCALE = 'en-US';

    /**
     * @var string the "modules" directory path
     */
    private $modulesDirectory;

    /**
     * @var ExternalModuleLegacySystemProvider
     */
    private $externalModuleLegacySystemProvider;

    /**
     * @var DatabaseTranslationLoader
     */
    private $databaseLoader;

    /**
     * @var string the resource directory
     */
    protected $resourceDirectory;

    /**
     * @var string the Catalogue locale
     */
    protected $locale;

    /**
     * @var string the Catalogue domain
     */
    protected $domain;

    public function __construct(
        DatabaseTranslationLoader $databaseLoader,
        ExternalModuleLegacySystemProvider $externalModuleLegacySystemProvider,
        $resourceDirectory,
        $modulesDirectory
    ) {
        $this->modulesDirectory = $modulesDirectory;
        $this->externalModuleLegacySystemProvider = $externalModuleLegacySystemProvider;
        $this->databaseLoader = $databaseLoader;
        $this->resourceDirectory = $resourceDirectory;
        $this->locale = self::DEFAULT_LOCALE;
    }

    /**
     * {@inheritdoc}
     */
    public function getDirectories(): array
    {
        return [$this->getResourceDirectory()];
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters(): array
    {
        return ['#^' . preg_quote($this->domain, '#') . '([A-Z]|\.|$)#'];
    }

    /**
     * {@inheritdoc}
     */
    public function getTranslationDomains(): array
    {
        return ['^' . preg_quote($this->domain) . '([A-Z]|$)'];
    }

    /**
     * {@inheritdoc}
     */
    public function getLocale(): string
    {
        return $this->locale;
    }

    /**
     * @param string $locale
     */
    public function setLocale(string $locale): self
    {
        $this->externalModuleLegacySystemProvider->setLocale($locale);
        $this->locale = $locale;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setDomain(string $domain): self
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
    public function getPrestaShopLocale(): string
    {
        @trigger_error(
            '`SearchProvider::getPrestaShopLocale` function is deprecated and will be removed in the next major',
            E_USER_DEPRECATED
        );

        return Converter::toPrestaShopLocale($this->locale);
    }

    /**
     * {@inheritdoc}
     */
    public function getMessageCatalogue(): MessageCatalogue
    {
        $messageCatalogue = $this->getDefaultCatalogue();

        $xlfCatalogue = $this->getXliffCatalogue();
        $messageCatalogue->addCatalogue($xlfCatalogue);

        $databaseCatalogue = $this->getDatabaseCatalogue();

        // Merge database catalogue to xliff catalogue
        $messageCatalogue->addCatalogue($databaseCatalogue);

        return $messageCatalogue;
    }

    /**
     * {@inheritdoc}
     *
     * @throws FileNotFoundException
     */
    public function getDefaultCatalogue(bool $empty = true): MessageCatalogue
    {
        try {
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
        } catch (FileNotFoundException $e) {
            $defaultCatalogue = $this->externalModuleLegacySystemProvider->getDefaultCatalogue($empty);
            $defaultCatalogue = $this->filterCatalogue($defaultCatalogue);
        }

        return $defaultCatalogue;
    }

    /**
     * {@inheritdoc}
     *
     * @throws FileNotFoundException
     */
    public function getXliffCatalogue(): MessageCatalogue
    {
        try {
            $xliffCatalogue = new MessageCatalogue($this->locale);

            foreach ($this->getFilters() as $filter) {
                $filteredCatalogue = $this->getCatalogueFromPaths(
                    $this->getDirectories(),
                    $this->locale,
                    $filter
                );
                $xliffCatalogue->addCatalogue($filteredCatalogue);
            }
        } catch (\Exception $e) {
            $xliffCatalogue = $this->externalModuleLegacySystemProvider->getXliffCatalogue();
            $xliffCatalogue = $this->filterCatalogue($xliffCatalogue);
        }

        return $xliffCatalogue;
    }

    /**
     * Get the Catalogue from database only.
     *
     * @param string|null $themeName
     *
     * @return MessageCatalogue A MessageCatalogue instance
     */
    public function getDatabaseCatalogue(string $themeName = null): MessageCatalogue
    {
        $databaseCatalogue = new MessageCatalogue($this->locale);

        foreach ($this->getTranslationDomains() as $translationDomain) {
            if (!($this->getDatabaseLoader() instanceof DatabaseTranslationLoader)) {
                continue;
            }
            $domainCatalogue = $this->getDatabaseLoader()->load($this->locale, $translationDomain, $themeName);

            if ($domainCatalogue instanceof MessageCatalogue) {
                $databaseCatalogue->addCatalogue($domainCatalogue);
            }
        }

        return $databaseCatalogue;
    }

    /**
     * @return string Path to app/Resources/translations/{locale}
     */
    public function getResourceDirectory(): string
    {
        return $this->resourceDirectory . DIRECTORY_SEPARATOR . $this->locale;
    }

    /**
     * @return DatabaseTranslationLoader
     */
    public function getDatabaseLoader(): DatabaseTranslationLoader
    {
        return $this->databaseLoader;
    }

    /**
     * Empties out the catalogue by removing translations but leaving keys
     *
     * @param MessageCatalogue $messageCatalogue
     *
     * @return MessageCatalogue Empty the catalogue
     */
    public function emptyCatalogue(MessageCatalogue $messageCatalogue): MessageCatalogue
    {
        foreach ($messageCatalogue->all() as $domain => $messages) {
            foreach (array_keys($messages) as $translationKey) {
                $messageCatalogue->set($translationKey, '', $domain);
            }
        }

        return $messageCatalogue;
    }

    /**
     * @param array $paths a list of paths when we can look for translations
     * @param string $locale the Symfony (not the PrestaShop one) locale
     * @param string|null $pattern a regular expression
     *
     * @return MessageCatalogue
     *
     * @throws FileNotFoundException
     */
    public function getCatalogueFromPaths(array $paths, string $locale, string $pattern = null): MessageCatalogue
    {
        return (new TranslationFinder())->getCatalogueFromPaths($paths, $locale, $pattern);
    }

    /**
     * Get domain.
     *
     * @deprecated since 1.7.6, to be removed in the next major
     *
     * @return string
     */
    public function getDomain(): string
    {
        @trigger_error(
            __METHOD__ . ' function is deprecated and will be removed in the next major',
            E_USER_DEPRECATED
        );

        return $this->domain;
    }

    /**
     * {@inheritdoc}
     */
    public function getIdentifier(): string
    {
        return 'search';
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultResourceDirectory(): string
    {
        return $this->resourceDirectory . DIRECTORY_SEPARATOR . 'default';
    }

    /**
     * @deprecated since 1.7.6, to be removed in the next major
     *
     * @return string
     */
    public function getModuleDirectory(): string
    {
        @trigger_error(
            __METHOD__ . ' function is deprecated and will be removed in the next major',
            E_USER_DEPRECATED
        );

        return $this->modulesDirectory;
    }

    /**
     * {@inheritdoc}
     */
    public function setModuleName(string $moduleName): void
    {
        $this->externalModuleLegacySystemProvider->setModuleName($moduleName);
    }

    /**
     * Filters the catalogue so that only domains matching the filters are kept
     *
     * @param MessageCatalogue $defaultCatalogue
     *
     * @return MessageCatalogue
     */
    private function filterCatalogue(MessageCatalogue $defaultCatalogue): MessageCatalogue
    {
        // return only elements whose domain matches the filters
        $filters = $this->getFilters();
        $allowedDomains = [];

        foreach ($defaultCatalogue->all() as $domain => $messages) {
            foreach ($filters as $filter) {
                if (preg_match($filter, $domain)) {
                    $allowedDomains[$domain] = $messages;
                    break;
                }
            }
        }

        $defaultCatalogue = new MessageCatalogue($this->getLocale(), $allowedDomains);

        return $defaultCatalogue;
    }
}
