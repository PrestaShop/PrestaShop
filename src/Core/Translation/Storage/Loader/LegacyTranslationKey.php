<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Translation\Storage\Loader;

use PrestaShop\PrestaShop\Core\Translation\Exception\InvalidLegacyTranslationKeyException;

/**
 * Parses a legacy translation key and returns its data
 */
class LegacyTranslationKey
{
    /**
     * @var string the expected format of a legacy translation key
     */
    public const LEGACY_TRANSLATION_FORMAT = '#\<\{(?<module>[\w-]+)\}(?<theme>[\w-]+)\>(?<source>[\.\w_-]+)_(?<hash>[0-9a-f]+)#';

    /**
     * @var string
     */
    private $module;

    /**
     * @var string
     */
    private $theme;

    /**
     * @var string
     */
    private $source;

    /**
     * @var string
     */
    private $hash;

    /**
     * @param string $module
     * @param string $theme
     * @param string $source
     * @param string $hash
     */
    public function __construct(string $module, string $theme, string $source, string $hash)
    {
        $this->module = $module;
        $this->theme = $theme;
        $this->source = $source;
        $this->hash = $hash;
    }

    /**
     * @return string
     */
    public function getModule(): string
    {
        return $this->module;
    }

    /**
     * @return string
     */
    public function getTheme(): string
    {
        return $this->theme;
    }

    /**
     * @return string
     */
    public function getSource(): string
    {
        return $this->source;
    }

    /**
     * @return string
     */
    public function getHash(): string
    {
        return $this->hash;
    }

    /**
     * Parses a legacy translation key and returns its data
     *
     * @param string $key Legacy translation key
     *
     * @return LegacyTranslationKey
     *
     * @throws InvalidLegacyTranslationKeyException
     */
    public static function buildFromString(string $key): LegacyTranslationKey
    {
        $matches = [];
        preg_match(self::LEGACY_TRANSLATION_FORMAT, $key, $matches);

        foreach (['module', 'theme', 'source', 'hash'] as $item) {
            if (!isset($matches[$item])) {
                throw new InvalidLegacyTranslationKeyException($item, $key);
            }
        }

        return new self($matches['module'], $matches['theme'], $matches['source'], $matches['hash']);
    }
}
