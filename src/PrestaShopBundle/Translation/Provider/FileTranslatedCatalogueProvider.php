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
use Symfony\Component\Translation\MessageCatalogue;
use Symfony\Component\Translation\MessageCatalogueInterface;

class FileTranslatedCatalogueProvider implements FileSystemCatalogueProviderInterface, TranslationCatalogueProviderInterface
{
    /**
     * @var string
     */
    private $directory;

    /**
     * @var array
     */
    private $filenameFilters = [];

    /**
     * @var string
     */
    private $locale;

    public function setDirectory(string $directory): FileSystemCatalogueProviderInterface
    {
        $this->directory = $directory;

        return $this;
    }

    public function setLocale(string $locale): FileSystemCatalogueProviderInterface
    {
        $this->locale = $locale;

        return $this;
    }

    /**
     * @param array $filenameFilters
     *
     * @return DefaultCatalogueProvider
     */
    public function setFilenameFilters(array $filenameFilters): FileSystemCatalogueProviderInterface
    {
        $this->filenameFilters = $filenameFilters;

        return $this;
    }

    public function getCatalogue(): MessageCatalogueInterface
    {
        if (null === $this->locale) {
            throw new \LogicException('Locale cannot be null. Call setLocale first');
        }

        $catalogue = new MessageCatalogue($this->locale);
        $translationFinder = new TranslationFinder();
        $localeResourceDirectory = $this->directory . DIRECTORY_SEPARATOR . $this->locale;

        foreach ($this->filenameFilters as $filter) {
            try {
                $filteredCatalogue = $translationFinder->getCatalogueFromPaths(
                    [$localeResourceDirectory],
                    $this->locale,
                    $filter
                );
                $catalogue->addCatalogue($filteredCatalogue);
            } catch (FileNotFoundException $e) {
                // there are no translation files, ignore them
            }
        }

        return $catalogue;
    }
}
