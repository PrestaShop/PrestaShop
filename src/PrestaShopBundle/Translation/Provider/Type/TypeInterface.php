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

namespace PrestaShopBundle\Translation\Provider\Type;

/**
 * Defines properties required for each provider.
 */
interface TypeInterface
{
    public const TYPE_MODULES = 'modules';
    public const TYPE_THEMES = 'themes';
    public const TYPE_MAILS = 'mails';
    public const TYPE_MAILS_BODY = 'mails_body';
    public const TYPE_BACK = 'back';
    public const TYPE_OTHERS = 'others';
    public const TYPE_CORE_FRONT = 'core_front';

    public const ACCEPTED_TYPES = [
        self::TYPE_MODULES,
        self::TYPE_THEMES,
        self::TYPE_MAILS,
        self::TYPE_MAILS_BODY,
        self::TYPE_BACK,
        self::TYPE_OTHERS,
    ];

    /**
     * Returns a list of patterns to filter catalogue files.
     * Depends on the translation type.
     *
     * @return string[]
     */
    public function getFilenameFilters(): array;

    /**
     * Returns a list of patterns to filter translation domains.
     * Depends on the translation type.
     *
     * @return string[]
     */
    public function getTranslationDomains(): array;
}
