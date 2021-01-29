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

use PrestaShopBundle\Translation\Loader\DatabaseTranslationLoader;
use Symfony\Component\Translation\MessageCatalogue;

class BackofficeCatalogueProvider implements CatalogueProviderInterface
{
    /**
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
     * @param DatabaseTranslationLoader $databaseTranslationLoader
     * @param string $resourceDirectory
     */
    public function __construct(
        DatabaseTranslationLoader $databaseTranslationLoader,
        string $resourceDirectory
    ) {
        $this->databaseTranslationLoader = $databaseTranslationLoader;
        $this->resourceDirectory = $resourceDirectory;
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultCatalogue(string $locale, bool $empty = true): MessageCatalogue
    {
        $provider = new DefaultCatalogueProvider(
            $this->resourceDirectory . DIRECTORY_SEPARATOR . 'default',
            $this->getFilenameFilters()
        );

        return $provider->getCatalogue($locale, $empty);
    }

    /**
     * {@inheritdoc}
     */
    public function getFileTranslatedCatalogue(string $locale): MessageCatalogue
    {
        $provider = new FileTranslatedCatalogueProvider(
            $this->resourceDirectory,
            $this->getFilenameFilters()
        );

        return $provider->getCatalogue($locale);
    }

    /**
     * {@inheritdoc}
     */
    public function getUserTranslatedCatalogue(string $locale): MessageCatalogue
    {
        $provider = new UserTranslatedCatalogueProvider(
            $this->databaseTranslationLoader,
            $this->getTranslationDomains()
        );

        return $provider->getCatalogue($locale);
    }

    /**
     * This is for Default and FileTranslated catalogue.
     * In the translations directory, we will take any file starting with 'Admin' and followed by alphabetical characters.
     *
     * @return string[]
     */
    protected function getFilenameFilters(): array
    {
        return [
            '#^Admin[A-Z]#',
        ];
    }

    /**
     * This is for UserTranslated catalogue.
     * In the translations table, we will take any translation having domain starting with 'Admin' and followed by alphabetical characters.
     *
     * @return string[]
     */
    protected function getTranslationDomains(): array
    {
        return [
            '^Admin[A-Z]',
        ];
    }
}
