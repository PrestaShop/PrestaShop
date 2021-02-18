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

namespace PrestaShop\PrestaShop\Core\Translation\Provider;

use PrestaShopBundle\Translation\Loader\DatabaseTranslationLoader;
use Symfony\Component\Translation\MessageCatalogue;

/**
 * Returns the 3 layers of translation catalogues related to the Backoffice interface translations.
 * The default catalogue is in app/Resources/translations/default, in any file starting with "Admin"
 * The file catalogue is in app/Resources/translations/LOCALE, in any file starting with "Admin"
 * The user catalogue is stored in DB, domain starting with Admin and theme is NULL.
 *
 * @see CatalogueLayersProviderInterface to understand the 3 layers.
 */
class CoreCatalogueLayersProvider implements CatalogueLayersProviderInterface
{
    /**
     * We need a connection to DB to load user translated catalogue.
     *
     * @var DatabaseTranslationLoader
     */
    private $databaseTranslationLoader;

    /**
     * This is the directory where Default and FileTranslated translations are stored.
     * For the Backoffice catalogue,
     *   - Default catalogue is within resourceDirectory/default
     *   - FileTranslated catalogue is in resourceDirectory/locale
     *
     * @var string
     */
    private $resourceDirectory;

    /**
     * @var DefaultCatalogueProvider
     */
    private $defaultCatalogueProvider;

    /**
     * @var FileTranslatedCatalogueProvider
     */
    private $fileTranslatedCatalogueProvider;

    /**
     * @var UserTranslatedCatalogueProvider
     */
    private $userTranslatedCatalogueProvider;
    /**
     * @var array
     */
    private $filenameFilters;
    /**
     * @var array
     */
    private $translationDomains;

    /**
     * @param DatabaseTranslationLoader $databaseTranslationLoader
     * @param string $resourceDirectory
     * @param string[] $filenameFilters
     * @param string[] $translationDomains
     */
    public function __construct(
        DatabaseTranslationLoader $databaseTranslationLoader,
        string $resourceDirectory,
        array $filenameFilters,
        array $translationDomains
    ) {
        $this->databaseTranslationLoader = $databaseTranslationLoader;
        $this->resourceDirectory = $resourceDirectory;
        $this->filenameFilters = $filenameFilters;
        $this->translationDomains = $translationDomains;
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultCatalogue(string $locale): MessageCatalogue
    {
        return $this->getDefaultCatalogueProvider()->getCatalogue($locale);
    }

    /**
     * {@inheritdoc}
     */
    public function getFileTranslatedCatalogue(string $locale): MessageCatalogue
    {
        return $this->getFileTranslatedCatalogueProvider()->getCatalogue($locale);
    }

    /**
     * {@inheritdoc}
     */
    public function getUserTranslatedCatalogue(string $locale): MessageCatalogue
    {
        return $this->getUserTranslatedCatalogueProvider()->getCatalogue($locale);
    }

    private function getDefaultCatalogueProvider(): DefaultCatalogueProvider
    {
        if (null === $this->defaultCatalogueProvider) {
            $this->defaultCatalogueProvider = new DefaultCatalogueProvider(
                $this->resourceDirectory . DIRECTORY_SEPARATOR . 'default',
                $this->filenameFilters
            );
        }

        return $this->defaultCatalogueProvider;
    }

    private function getFileTranslatedCatalogueProvider(): FileTranslatedCatalogueProvider
    {
        if (null === $this->fileTranslatedCatalogueProvider) {
            $this->fileTranslatedCatalogueProvider = new FileTranslatedCatalogueProvider(
                $this->resourceDirectory,
                $this->filenameFilters
            );
        }

        return $this->fileTranslatedCatalogueProvider;
    }

    private function getUserTranslatedCatalogueProvider(): UserTranslatedCatalogueProvider
    {
        if (null === $this->userTranslatedCatalogueProvider) {
            $this->userTranslatedCatalogueProvider = new UserTranslatedCatalogueProvider(
                $this->databaseTranslationLoader,
                $this->translationDomains
            );
        }

        return $this->userTranslatedCatalogueProvider;
    }
}
