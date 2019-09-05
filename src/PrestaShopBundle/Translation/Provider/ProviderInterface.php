<?php

/**
 * 2007-2019 PrestaShop and Contributors
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
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShopBundle\Translation\Provider;

use Symfony\Component\Translation\MessageCatalogue;

/**
 * Define contract to retrieve translations.
 */
interface ProviderInterface
{
    /**
     * @return string[] List of directories to parse
     */
    public function getDirectories();

    /**
     * Returns a list of patterns for catalogue domain filtering (including XLF file lookup)
     *
     * @return string[]
     */
    public function getFilters();

    /**
     * Returns a list of patterns for translation domains to get from database.
     *
     * @return string[] List of Mysql compatible regexes (no regex delimiter)
     */
    public function getTranslationDomains();

    /**
     * @return string Locale used to build the MessageCatalogue
     */
    public function getLocale();

    /**
     * @return MessageCatalogue A provider must return a MessageCatalogue
     */
    public function getMessageCatalogue();

    /**
     * @return string Unique identifier
     */
    public function getIdentifier();
}
