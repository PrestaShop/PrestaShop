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

class FileTranslatedCatalogueProvider implements TranslationCatalogueProviderInterface, FileTranslatedCatalogueProviderInterface
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

    /**
     * DefaultCatalogueProvider constructor.
     *
     * @param string $locale
     * @param string $directory
     * @param array $filenameFilters
     */
    public function __construct(string $locale, string $directory, array $filenameFilters)
    {
        $this->locale = $locale;
        $this->directory = $directory;
        $this->filenameFilters = $filenameFilters;
    }

    /**
     * @return MessageCatalogueInterface
     */
    public function getCatalogue(): MessageCatalogueInterface
    {
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

    /**
     * @return MessageCatalogueInterface
     */
    public function getFilesystemCatalogue(): MessageCatalogueInterface
    {
        return $this->getCatalogue();
    }
}
