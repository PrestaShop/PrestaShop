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

use Symfony\Component\Translation\MessageCatalogue;
use Symfony\Component\Translation\MessageCatalogueInterface;

interface ProviderInterface
{
    /**
     * @param string $locale
     */
    public function setLocale(string $locale);

    /**
     * Identifier for the providers
     *
     * @return string
     */
    public function getIdentifier(): string;

    /**
     * @return MessageCatalogueInterface
     */
    public function getMessageCatalogue(): MessageCatalogueInterface;

    /**
     * Get the default (aka untranslated) catalogue
     *
     * @param bool $empty if true, empty the catalogue values (keep the keys)
     *
     * @return MessageCatalogueInterface Return a default catalogue with all keys
     */
    public function getDefaultCatalogue(bool $empty = true);

    /**
     * @return MessageCatalogue
     */
    public function getXliffCatalogue();

    /**
     * @param string|null $themeName the Theme name
     *
     * @return MessageCatalogue
     */
    public function getDatabaseCatalogue($themeName = null);
}
