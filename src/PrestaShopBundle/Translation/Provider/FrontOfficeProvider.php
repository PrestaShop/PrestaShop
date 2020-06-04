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
     * @var string locale
     */
    protected $locale;
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
     *
     * @return FrontOfficeProvider
     */
    public function setLocale(string $locale): FrontOfficeProvider
    {
        $this->locale = $locale;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getIdentifier()
    {
        return 'front';
    }

    /**
     * @return MessageCatalogueInterface
     *
     * @throws FileNotFoundException
     */
    public function getMessageCatalogue(): MessageCatalogueInterface
    {
        if (null === $this->locale) {
            throw new \LogicException('Locale cannot be null. Call setLocale first');
        }

        $messageCatalogue = $this->getDefaultCatalogue();

        // Merge catalogues

        $xlfCatalogue = $this->getFilesystemCatalogue();
        $messageCatalogue->addCatalogue($xlfCatalogue);
        unset($xlfCatalogue);

        $databaseCatalogue = $this->getUserTranslatedCatalogue();
        $messageCatalogue->addCatalogue($databaseCatalogue);
        unset($databaseCatalogue);

        return $messageCatalogue;
    }

    /**
     * @param bool $empty
     *
     * @return MessageCatalogueInterface
     *
     * @throws FileNotFoundException
     */
    public function getDefaultCatalogue(bool $empty = true): MessageCatalogueInterface
    {
        return (new DefaultCatalogueProvider())
            ->setFilenameFilters($this->getFilenameFilters())
            ->setDirectory($this->resourceDirectory . DIRECTORY_SEPARATOR . 'default')
            ->setLocale($this->locale)
            ->getCatalogue($empty);
    }

    /**
     * @return MessageCatalogueInterface
     *
     * @throws FileNotFoundException
     */
    public function getFilesystemCatalogue(): MessageCatalogueInterface
    {
        return (new FileTranslatedCatalogueProvider())
            ->setDirectory($this->resourceDirectory)
            ->setFilenameFilters($this->getFilenameFilters())
            ->setLocale($this->locale)
            ->getCatalogue();
    }

    /**
     * @param string|null $themeName
     *
     * @return MessageCatalogueInterface
     */
    public function getUserTranslatedCatalogue(string $themeName = null): MessageCatalogueInterface
    {
        if (null === $themeName) {
            $themeName = self::DEFAULT_THEME_NAME;
        }

        $translationDomains = [
            '^Shop*',
            '^Modules(.*)Shop',
        ];

        return (new UserTranslatedCatalogueProvider($this->databaseLoader))
            ->setTranslationDomains($translationDomains)
            ->setLocale($this->locale)
            ->setTheme($themeName)
            ->getCatalogue();
    }

    /**
     * @return string[]
     */
    private function getFilenameFilters()
    {
        return [
            '#^Shop*#',
            '#^Modules(.*)Shop#',
        ];
    }
}
