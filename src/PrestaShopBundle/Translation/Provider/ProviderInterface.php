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
     * @return MessageCatalogueInterface A provider must return a MessageCatalogue
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
}
