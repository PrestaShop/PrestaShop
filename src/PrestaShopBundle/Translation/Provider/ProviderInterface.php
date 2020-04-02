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

namespace PrestaShopBundle\Translation\Provider;

use Symfony\Component\Translation\MessageCatalogueInterface;

/**
 * Define contract to retrieve translations.
 */
interface ProviderInterface
{
    /**
     * Returns a list of patterns used to choose which wordings will be imported from database.
     * Patterns from this list will be run against translation domains.
     *
     * @return string[] List of Mysql compatible regexes (no regex delimiter)
     */
    public function getTranslationDomains();

    /**
     * Returns the locale used to build the MessageCatalogue
     *
     * @return string
     */
    public function getLocale();

    /**
     * Defines the locale to work with
     *
     * @param string $locale
     *
     * @return static
     */
    public function setLocale($locale);

    /**
     * Get the Catalogue from database only.
     *
     * @param string|null $themeName Theme name
     *
     * @return MessageCatalogueInterface
     */
    public function getDatabaseCatalogue($themeName = null);

    /**
     * Returns the default (aka not translated) catalogue
     *
     * @param bool $empty [default=true] Remove translations and return an empty catalogue
     *
     * @return MessageCatalogueInterface
     */
    public function getDefaultCatalogue($empty = true);

    /**
     * Returns the translated message catalogue
     *
     * @return MessageCatalogueInterface
     */
    public function getMessageCatalogue();

    /**
     * Returns the catalogue from Xliff files only.
     *
     * @return MessageCatalogueInterface
     */
    public function getXliffCatalogue();

    /**
     * Returns the provider's unique identifier
     *
     * @return string
     */
    public function getIdentifier();

    /**
     * Returns the path to the default resources directory.
     *
     * Most of the time, it's `app/Resources/translations/default/{locale}`
     *
     * @return string
     */
    public function getDefaultResourceDirectory();
}
