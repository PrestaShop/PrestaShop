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

use Symfony\Component\Translation\MessageCatalogue;
use Symfony\Component\Translation\MessageCatalogueInterface;

class DefaultCatalogueProvider implements FileSystemCatalogueProviderInterface, TranslationCatalogueProviderInterface
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

    public function setLocale(?string $locale): FileSystemCatalogueProviderInterface
    {
        $this->locale = $locale;

        return $this;
    }

    /**
     * @param array $filenameFilters
     *
     * @return DefaultCatalogueProvider
     */
    public function setFilenameFilters(array $filenameFilters): DefaultCatalogueProvider
    {
        $this->filenameFilters = $filenameFilters;

        return $this;
    }

    public function getCatalogue(bool $empty = true): MessageCatalogueInterface
    {
        $defaultCatalogue = new MessageCatalogue($this->locale);
        $translationFinder = new TranslationFinder();

        foreach ($this->filenameFilters as $filter) {
            $filteredCatalogue = $translationFinder->getCatalogueFromPaths(
                [$this->directory],
                $this->locale,
                $filter
            );
            $defaultCatalogue->addCatalogue($filteredCatalogue);
        }

        if ($empty && $this->locale !== AbstractProvider::DEFAULT_LOCALE) {
            $defaultCatalogue = $this->emptyCatalogue($defaultCatalogue);
        }

        return $defaultCatalogue;
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
