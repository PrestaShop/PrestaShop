<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
