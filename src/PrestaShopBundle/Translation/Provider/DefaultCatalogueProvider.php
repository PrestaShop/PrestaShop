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
use Symfony\Component\Translation\MessageCatalogue;

/**
 * Gets catalogue within the files filtered by name in the directory given.
 * The Default catalogue is the base wording, in english, and stored in filesystem or extracted from templates.
 */
class DefaultCatalogueProvider extends AbstractCatalogueProvider
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
     * @param string $directory Directory where to look files
     * @param string[] $filenameFilters Array of globs to use to match files
     *
     * @throws FileNotFoundException
     */
    public function __construct(string $directory, array $filenameFilters)
    {
        if (!is_dir($directory) || !is_readable($directory)) {
            throw new FileNotFoundException(sprintf('Directory %s does not exist', $directory));
        }

        if (!$this->assertIsArrayOfString($filenameFilters)) {
            throw new \InvalidArgumentException('Given filename filters are invalid. An array of strings was expected.');
        }

        $this->directory = $directory;
        $this->filenameFilters = $filenameFilters;
    }

    /**
     * Returns the translation catalogue for the provided locale
     *
     * @param string $locale
     * @param bool $empty
     *
     * @return MessageCatalogue
     *
     * @throws FileNotFoundException
     */
    public function getCatalogue(string $locale, bool $empty = true): MessageCatalogue
    {
        $defaultCatalogue = new MessageCatalogue($locale);
        $translationFinder = new TranslationFinder();

        foreach ($this->filenameFilters as $filter) {
            $filteredCatalogue = $translationFinder->getCatalogueFromPaths(
                [$this->directory],
                $locale,
                $filter
            );
            $defaultCatalogue->addCatalogue($filteredCatalogue);
        }

        // Emptying the catalogue is only relevant if $locale given equals the DEFAULT LOCALE
        // because, in that case, the translation key is equal to its value
        // Otherwise, there is a problem in the locale given or in the default catalogue.
        // Example: If DEFAULT_LOCALE = english $translation = ['This is the text' => 'This is the text'],
        // In french it'll be ['This is the text' => 'Ceci est le texte']. Emptying this will make you loose the translation
        if ($empty && $locale !== CatalogueProviderInterface::DEFAULT_LOCALE) {
            $defaultCatalogue = $this->emptyCatalogue($defaultCatalogue);
        }

        return $defaultCatalogue;
    }

    /**
     * Empties out the catalogue by removing translations but leaving keys
     *
     * @param MessageCatalogue $messageCatalogue
     *
     * @return MessageCatalogue Empty the catalogue
     */
    protected function emptyCatalogue(MessageCatalogue $messageCatalogue): MessageCatalogue
    {
        foreach ($messageCatalogue->all() as $domain => $messages) {
            foreach (array_keys($messages) as $translationKey) {
                $messageCatalogue->set($translationKey, '', $domain);
            }
        }

        return $messageCatalogue;
    }
}
