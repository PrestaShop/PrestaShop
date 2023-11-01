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

namespace PrestaShopBundle\Translation\Loader;

use PrestaShopBundle\Translation\Exception\InvalidLegacyTranslationKeyException;

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
     * Parses a legacy translation key and returns its data
     *
     * @param string $key Legacy translation key
     *
     * @return LegacyTranslationKey
     *
     * @throws InvalidLegacyTranslationKeyException
     */
    public static function buildFromString($key)
    {
        $matches = [];
        preg_match(self::LEGACY_TRANSLATION_FORMAT, $key, $matches);

        foreach (['module', 'theme', 'source', 'hash'] as $item) {
            if (!isset($matches[$item])) {
                throw InvalidLegacyTranslationKeyException::missingElementFromKey($item, $key);
            }
        }

        return new self($matches['module'], $matches['theme'], $matches['source'], $matches['hash']);
    }

    /**
     * @param string $module
     * @param string $theme
     * @param string $source
     * @param string $hash
     */
    public function __construct($module, $theme, $source, $hash)
    {
        $this->module = $module;
        $this->theme = $theme;
        $this->source = $source;
        $this->hash = $hash;
    }

    /**
     * @return string
     */
    public function getModule()
    {
        return $this->module;
    }

    /**
     * @return string
     */
    public function getTheme()
    {
        return $this->theme;
    }

    /**
     * @return string
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * @return string
     */
    public function getHash()
    {
        return $this->hash;
    }
}
