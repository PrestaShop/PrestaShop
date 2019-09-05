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

namespace PrestaShopBundle\Translation\Factory;

use Symfony\Component\Translation\MessageCatalogueInterface;

interface TranslationsFactoryInterface
{
    const DEFAULT_LOCALE = 'en_US';

    /**
     * Generates extract of global Catalogue, using domain's identifiers.
     *
     * @param string $identifier Domain identifier
     * @param string $locale Locale identifier
     *
     * @throws ProviderNotFoundException
     *
     * @return MessageCatalogueInterface
     */
    public function createCatalogue($identifier, $locale = self::DEFAULT_LOCALE);

    /**
     * Generates Translation tree in Back Office.
     *
     * @param string $domainIdentifier Domain identifier
     * @param string $locale Locale identifier
     * @param null $theme
     * @param null $search
     *
     * @throws ProviderNotFoundException
     *
     * @return array Translation tree structure
     */
    public function createTranslationsArray($domainIdentifier, $locale = self::DEFAULT_LOCALE, $theme = null, $search = null);
}
