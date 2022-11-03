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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */
declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Translation\Storage\Provider;

use PrestaShop\PrestaShop\Core\Translation\Exception\TranslationFilesNotFoundException;
use PrestaShop\PrestaShop\Core\Translation\Storage\Loader\DatabaseTranslationLoader;
use PrestaShop\PrestaShop\Core\Translation\Storage\Provider\Finder\DefaultCatalogueFinder;
use PrestaShop\PrestaShop\Core\Translation\Storage\Provider\Finder\FileTranslatedCatalogueFinder;
use PrestaShop\PrestaShop\Core\Translation\Storage\Provider\Finder\UserTranslatedCatalogueFinder;
use Symfony\Component\Translation\MessageCatalogue;

/**
 * Returns the 3 layers of translation catalogues related to the Core interface translations.
 * The files pattern depends on the desired Type
 * The default catalogue is in app/Resources/translations/default, in any file starting with "files pattern"
 * The file catalogue is in app/Resources/translations/LOCALE, in any file starting with "files pattern"
 * The user catalogue is stored in DB, domain starting with "files pattern" and theme is NULL.
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
     *   - Default catalogue is within resourceDirectory/default
     *   - FileTranslated catalogue is in resourceDirectory/locale
     *
     * @var string
     */
    private $resourceDirectory;

    /**
     * @var DefaultCatalogueFinder
     */
    private $defaultCatalogueFinder;

    /**
     * @var FileTranslatedCatalogueFinder
     */
    private $fileTranslatedCatalogueFinder;

    /**
     * @var UserTranslatedCatalogueFinder
     */
    private $userTranslatedCatalogueFinder;

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
     * @param array<int, string> $filenameFilters
     * @param array<int, string> $translationDomains
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
     *
     * @throws TranslationFilesNotFoundException
     */
    public function getDefaultCatalogue(string $locale): MessageCatalogue
    {
        return $this->getDefaultCatalogueFinder()->getCatalogue($locale);
    }

    /**
     * {@inheritdoc}
     */
    public function getFileTranslatedCatalogue(string $locale): MessageCatalogue
    {
        return $this->getFileTranslatedCatalogueFinder()->getCatalogue($locale);
    }

    /**
     * {@inheritdoc}
     */
    public function getUserTranslatedCatalogue(string $locale): MessageCatalogue
    {
        return $this->getUserTranslatedCatalogueFinder()->getCatalogue($locale);
    }

    /**
     * @return DefaultCatalogueFinder
     *
     * @throws TranslationFilesNotFoundException
     */
    private function getDefaultCatalogueFinder(): DefaultCatalogueFinder
    {
        if (null === $this->defaultCatalogueFinder) {
            $this->defaultCatalogueFinder = new DefaultCatalogueFinder(
                $this->resourceDirectory . DIRECTORY_SEPARATOR . 'default',
                $this->filenameFilters
            );
        }

        return $this->defaultCatalogueFinder;
    }

    /**
     * @return FileTranslatedCatalogueFinder
     *
     * @throws TranslationFilesNotFoundException
     */
    private function getFileTranslatedCatalogueFinder(): FileTranslatedCatalogueFinder
    {
        if (null === $this->fileTranslatedCatalogueFinder) {
            $this->fileTranslatedCatalogueFinder = new FileTranslatedCatalogueFinder(
                $this->resourceDirectory,
                $this->filenameFilters
            );
        }

        return $this->fileTranslatedCatalogueFinder;
    }

    /**
     * @return UserTranslatedCatalogueFinder
     */
    private function getUserTranslatedCatalogueFinder(): UserTranslatedCatalogueFinder
    {
        if (null === $this->userTranslatedCatalogueFinder) {
            $this->userTranslatedCatalogueFinder = new UserTranslatedCatalogueFinder(
                $this->databaseTranslationLoader,
                $this->translationDomains
            );
        }

        return $this->userTranslatedCatalogueFinder;
    }
}
