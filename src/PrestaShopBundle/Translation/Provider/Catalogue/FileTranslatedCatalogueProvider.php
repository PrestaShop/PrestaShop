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

namespace PrestaShopBundle\Translation\Provider\Catalogue;

use PrestaShop\PrestaShop\Core\Exception\FileNotFoundException;
use PrestaShopBundle\Translation\Provider\TranslationFinder;
use Symfony\Component\Translation\MessageCatalogue;
use Symfony\Component\Translation\MessageCatalogueInterface;

/**
 * Gets catalogue within the files filtered by name in the directory given.
 * The translation files are searched in the subdirectory with the language name.
 */
class FileTranslatedCatalogueProvider implements TranslationCatalogueProviderInterface
{
    /**
     * @var string
     */
    private $directory;

    /**
     * @var array
     */
    private $filenameFilters;

    /**
     * @param string $directory
     * @param array $filenameFilters
     */
    public function __construct(string $directory, array $filenameFilters)
    {
        $this->directory = $directory;
        $this->filenameFilters = $filenameFilters;
    }

    /**
     * {@inheritdoc}
     *
     * @throws FileNotFoundException
     */
    public function getCatalogue(string $locale): MessageCatalogueInterface
    {
        $catalogue = new MessageCatalogue($locale);
        $translationFinder = new TranslationFinder();
        $localeResourceDirectory = rtrim($this->directory, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $locale;

        foreach ($this->filenameFilters as $filter) {
            $filteredCatalogue = $translationFinder->getCatalogueFromPaths(
                [$localeResourceDirectory],
                $locale,
                $filter
            );
            $catalogue->addCatalogue($filteredCatalogue);
        }

        return $catalogue;
    }

    /**
     * @param string $locale
     *
     * @return MessageCatalogueInterface
     *
     * @throws FileNotFoundException
     */
    public function getFileTranslatedCatalogue(string $locale): MessageCatalogueInterface
    {
        return $this->getCatalogue($locale);
    }
}
