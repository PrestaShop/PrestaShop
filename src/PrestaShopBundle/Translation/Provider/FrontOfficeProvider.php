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

namespace PrestaShopBundle\Translation\Provider;

use PrestaShop\PrestaShop\Core\Exception\FileNotFoundException;
use PrestaShopBundle\Translation\Loader\DatabaseTranslationLoader;
use Symfony\Component\Translation\MessageCatalogue;
use Symfony\Component\Translation\MessageCatalogueInterface;

/**
 * Main translation provider for the Front Office
 */
class FrontOfficeProvider implements ProviderInterface
{
    const DEFAULT_THEME_NAME = 'classic';

    /**
     * @var string Path where translation files are found
     */
    protected $resourceDirectory;

    /**
     * @var DatabaseTranslationLoader
     */
    private $databaseLoader;

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
     */
    public function getDefaultCatalogue(string $locale, bool $empty = true): MessageCatalogueInterface
    {
        return (new DefaultCatalogueProvider(
            $this->resourceDirectory . DIRECTORY_SEPARATOR . 'default',
            $this->getFilenameFilters()
        ))
            ->getCatalogue($locale, $empty);
    }

    /**
     * @param string $locale
     *
     * @return MessageCatalogueInterface
     */
    public function getFileTranslatedCatalogue(string $locale): MessageCatalogueInterface
    {
        try {
            return (new FileTranslatedCatalogueProvider(
                $this->resourceDirectory,
                $this->getFilenameFilters()
            ))
                ->getCatalogue($locale);
        } catch (FileNotFoundException $e) {
            // there are no translation files, ignore them
            return new MessageCatalogue($locale);
        }
    }

    /**
     * @param string $locale
     * @param string|null $themeName
     *
     * @return MessageCatalogueInterface
     */
    public function getUserTranslatedCatalogue(string $locale, ?string $themeName = null): MessageCatalogueInterface
    {
        if (null === $themeName) {
            $themeName = self::DEFAULT_THEME_NAME;
        }

        $translationDomains = [
            '^Shop*',
            '^Modules(.*)Shop',
        ];

        return (new UserTranslatedCatalogueProvider(
            $this->databaseLoader,
            $translationDomains
        ))
            ->getCatalogue($locale, $themeName);
    }

    /**
     * @return string[]
     */
    private function getFilenameFilters(): array
    {
        return [
            '#^Shop*#',
            '#^Modules(.*)Shop#',
        ];
    }
}
