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

namespace PrestaShop\PrestaShop\Core\Translation\Storage\Extractor;

use Symfony\Component\Translation\MessageCatalogue;

/**
 * Parse code content of module, searching for l() calls and retrieve
 * a Message Catalogue with all the keys and translations.
 */
interface LegacyModuleExtractorInterface
{
    /**
     * Extracts the wordings from source code and returns the translation messages.
     * Note that domain names will contain separating dots.
     *
     * @param string $moduleName
     * @param string $locale The locale used for the message catalogue. Note that wordings won't be translated in this locale.
     *
     * @return MessageCatalogue
     */
    public function extract(string $moduleName, string $locale): MessageCatalogue;
}
