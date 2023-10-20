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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace PrestaShop\PrestaShop\Core\Translation\Storage\Provider\Definition;

/**
 * Defines translation type and any element to know how and where to find translations catalogue
 */
interface ProviderDefinitionInterface
{
    public const TYPE_BACK = 'back';
    public const TYPE_FRONT = 'front';
    public const TYPE_MAILS = 'mails';
    public const TYPE_MAILS_BODY = 'mails_body';
    public const TYPE_OTHERS = 'others';
    public const TYPE_MODULES = 'modules';
    public const TYPE_THEMES = 'themes';
    public const TYPE_CORE_DOMAIN = 'core_domain';

    public const ALLOWED_TYPES = [
        self::TYPE_BACK,
        self::TYPE_FRONT,
        self::TYPE_MAILS,
        self::TYPE_MAILS_BODY,
        self::TYPE_OTHERS,
        self::TYPE_MODULES,
        self::TYPE_THEMES,
        self::TYPE_CORE_DOMAIN,
    ];

    public const ALLOWED_EXPORT_TYPES = [
        self::TYPE_BACK,
        self::TYPE_FRONT,
        self::TYPE_MAILS,
        self::TYPE_MAILS_BODY,
        self::TYPE_OTHERS,
        self::TYPE_MODULES,
        self::TYPE_THEMES,
    ];

    /**
     * @return string
     */
    public function getType(): string;

    /**
     * Returns a list of patterns to filter catalogue files.
     * Depends on the translation type.
     *
     * @return array<int, string>
     */
    public function getFilenameFilters(): array;

    /**
     * Returns a list of patterns to filter translation domains.
     * Depends on the translation type.
     *
     * @return array<int, string>
     */
    public function getTranslationDomains(): array;
}
