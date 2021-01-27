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
            $defaultCatalogue = (new DefaultCatalogueProvider(
                [$this->resourceDirectory . DIRECTORY_SEPARATOR . 'default'],
                $this->getFilters()
            ))
                ->getCatalogue($this->locale, $empty);
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
            $xliffCatalogue = (new XliffCatalogueProvider(
                [$this->resourceDirectory . DIRECTORY_SEPARATOR . $this->locale],
                $this->getFilters()
            ))
                ->getCatalogue($this->locale);
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
        return (new DatabaseCatalogueProvider($this->databaseLoader, $this->getTranslationDomains()))
            ->getCatalogue($this->locale, $themeName);
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
