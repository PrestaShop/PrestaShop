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

declare(strict_types=1);

namespace PrestaShopBundle\Translation\Provider;

use PrestaShop\PrestaShop\Core\Exception\FileNotFoundException;
use PrestaShopBundle\Translation\Loader\DatabaseTranslationLoader;
use Symfony\Component\Translation\MessageCatalogueInterface;

abstract class AbstractProvider implements ProviderInterface
{
    const DEFAULT_LOCALE = 'en-US';

    /**
     * @var string Path where translation files are found
     */
    protected $resourceDirectory;

    /**
     * @var string Catalogue domain
     */
    protected $domain;

    /**
     * @var string locale
     */
    protected $locale;

    /**
     * @var ExtractorInterface
     */
    protected $defaultCatalogueExtractor;
    /**
     * @var ExtractorInterface
     */
    protected $filesystemCatalogueExtractor;
    /**
     * @var UserTranslatedCatalogueExtractor
     */
    protected $userTranslatedCatalogueExtractor;

    /**
     * @param DatabaseTranslationLoader $databaseLoader
     * @param string $resourceDirectory Path where translations are found
     * @param array $translationDomains
     * @param array $filenameFilters
     * @param string $defaultResourceDirectory
     */
    public function __construct(
        DatabaseTranslationLoader $databaseLoader,
        string $resourceDirectory,
        array $translationDomains = [''],
        array $filenameFilters = [],
        string $defaultResourceDirectory = ''
    ) {
        $this->resourceDirectory = $resourceDirectory;
        $this->defaultCatalogueExtractor = (new DefaultCatalogueExtractor())
            ->setFilenameFilters($filenameFilters)
            ->setDefaultResourceDirectory($defaultResourceDirectory);

        $this->filesystemCatalogueExtractor = (new FilesystemCatalogueExtractor())
            ->setFilenameFilters($filenameFilters);

        $this->userTranslatedCatalogueExtractor = (new UserTranslatedCatalogueExtractor($databaseLoader))
            ->setTranslationDomains($translationDomains);
    }

    /**
     * @param string $domain
     *
     * @return static
     */
    public function setDomain(string $domain): self
    {
        $this->domain = $domain;

        return $this;
    }

    /**
     * @param string $locale
     *
     * @return static
     */
    public function setLocale(string $locale): self
    {
        $this->locale = $locale;

        return $this;
    }

    /**
     * @return string
     */
    public function getLocale(): string
    {
        return $this->locale;
    }

    /**
     * {@inheritdoc}
     */
    public function getMessageCatalogue(): MessageCatalogueInterface
    {
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
     * {@inheritdoc}
     *
     * @throws FileNotFoundException
     */
    public function getDefaultCatalogue(bool $empty = true): MessageCatalogueInterface
    {
        return $this->defaultCatalogueExtractor
            ->setLocale($this->getLocale())
            ->extract($empty);
    }

    /**
     * {@inheritdoc}
     */
    public function getFilesystemCatalogue(): MessageCatalogueInterface
    {
        return $this->filesystemCatalogueExtractor
            ->setLocale($this->getLocale())
            ->setResourceDirectory($this->resourceDirectory)
            ->extract();
    }

    /**
     * {@inheritdoc}
     */
    public function getUserTranslatedCatalogue(string $theme = null): MessageCatalogueInterface
    {
        return $this->userTranslatedCatalogueExtractor
            ->setLocale($this->getLocale())
            ->setTheme($theme)
            ->extract();
    }

    /**
     * Empties out the catalogue by removing translations but leaving keys
     *
     * @param MessageCatalogueInterface $messageCatalogue
     *
     * @return MessageCatalogueInterface Empty the catalogue
     */
    protected function emptyCatalogue(MessageCatalogueInterface $messageCatalogue): MessageCatalogueInterface
    {
        foreach ($messageCatalogue->all() as $domain => $messages) {
            foreach (array_keys($messages) as $translationKey) {
                $messageCatalogue->set($translationKey, '', $domain);
            }
        }

        return $messageCatalogue;
    }
}
