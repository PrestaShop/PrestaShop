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
use PrestaShopBundle\Exception\NotImplementedException;
use PrestaShopBundle\Translation\Loader\DatabaseTranslationLoader;
use Symfony\Component\Translation\MessageCatalogueInterface;

/**
 * Abstract class for the generic catalogue providers.
 */
abstract class TranslationsProvider implements ProviderInterface
{
    /**
     * @var DatabaseTranslationLoader
     */
    private $databaseLoader;

    /**
     * @var string
     */
    private $resourceDirectory;
    /**
     * @var string|null
     */
    private $domain;

    /**
     * @param DatabaseTranslationLoader $databaseLoader
     * @param string $resourceDirectory
     */
    public function __construct(
        DatabaseTranslationLoader $databaseLoader,
        string $resourceDirectory
    ) {
        $this->databaseLoader = $databaseLoader;
        $this->resourceDirectory = $resourceDirectory;
    }

    /**
     * @param string $locale
     * @param bool $empty
     *
     * @return MessageCatalogueInterface
     *
     * @throws FileNotFoundException
     * @throws NotImplementedException
     */
    public function getDefaultCatalogue(string $locale, bool $empty = true): MessageCatalogueInterface
    {
        $provider = new DefaultCatalogueProvider(
            $this->resourceDirectory . DIRECTORY_SEPARATOR . 'default',
            $this->getFilenameFilters()
        );

        return $provider->getDefaultCatalogue($locale, $empty);
    }

    /**
     * @param string $locale
     *
     * @return MessageCatalogueInterface
     *
     * @throws FileNotFoundException
     * @throws NotImplementedException
     */
    public function getFileTranslatedCatalogue(string $locale): MessageCatalogueInterface
    {
        $provider = new FileTranslatedCatalogueProvider(
            $this->resourceDirectory,
            $this->getFilenameFilters()
        );

        return $provider->getFileTranslatedCatalogue($locale);
    }

    /**
     * @param string $locale
     *
     * @return MessageCatalogueInterface
     *
     * @throws NotImplementedException
     */
    public function getUserTranslatedCatalogue(string $locale): MessageCatalogueInterface
    {
        $provider = new UserTranslatedCatalogueProvider(
            $this->databaseLoader,
            $this->getTranslationDomains()
        );

        return $provider->getUserTranslatedCatalogue($locale);
    }

    /**
     * @return array|string[]
     *
     * @throws NotImplementedException
     */
    protected function getFilenameFilters(): array
    {
        throw new NotImplementedException('You must implement the method in the specific class');
    }

    /**
     * @return array|string[]
     *
     * @throws NotImplementedException
     */
    protected function getTranslationDomains(): array
    {
        throw new NotImplementedException('You must implement the method in the specific class');
    }
}
