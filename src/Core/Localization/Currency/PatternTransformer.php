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

namespace PrestaShop\PrestaShop\Core\Localization\Currency;

use PrestaShop\PrestaShop\Core\Exception\InvalidArgumentException;

/**
 * Transform a currency pattern by moving the symbol position, with or without
 * a separation space (no-break space).
 */
class PatternTransformer
{
    public const NO_BREAK_SPACE = "\u{00A0}";
    public const RTL_CHARACTER = "\u{200F}";
    public const REGULAR_SPACE = ' ';
    public const CURRENCY_SYMBOL = '¤';

    public const TYPE_LEFT_SYMBOL_WITH_SPACE = 'leftWithSpace';
    public const TYPE_LEFT_SYMBOL_WITHOUT_SPACE = 'leftWithoutSpace';
    public const TYPE_RIGHT_SYMBOL_WITH_SPACE = 'rightWithSpace';
    public const TYPE_RIGHT_SYMBOL_WITHOUT_SPACE = 'rightWithoutSpace';

    public const ALLOWED_TRANSFORMATIONS = [
        self::TYPE_LEFT_SYMBOL_WITH_SPACE,
        self::TYPE_LEFT_SYMBOL_WITHOUT_SPACE,
        self::TYPE_RIGHT_SYMBOL_WITH_SPACE,
        self::TYPE_RIGHT_SYMBOL_WITHOUT_SPACE,
    ];

    public const CHARACTERS_TO_TRIM =
        self::CURRENCY_SYMBOL .
        self::NO_BREAK_SPACE .
        self::REGULAR_SPACE .
        self::RTL_CHARACTER
    ;

    public const TRANSFORM_DICTIONARY = [
        self::TYPE_LEFT_SYMBOL_WITH_SPACE => '$rtl$currencySymbol$nbsp$pattern',
        self::TYPE_LEFT_SYMBOL_WITHOUT_SPACE => '$rtl$currencySymbol$pattern',
        self::TYPE_RIGHT_SYMBOL_WITH_SPACE => '$rtl$pattern$nbsp$currencySymbol',
        self::TYPE_RIGHT_SYMBOL_WITHOUT_SPACE => '$rtl$pattern$currencySymbol',
    ];

    /**
     * @param string $currencyPattern
     * @param string $transformationType
     *
     * @return string
     *
     * @throws InvalidArgumentException
     */
    public function transform(string $currencyPattern, string $transformationType): string
    {
        if (!in_array($transformationType, self::ALLOWED_TRANSFORMATIONS)) {
            throw new InvalidArgumentException(sprintf('Invalid transformation type "%s", allowed transformations are: %s', $transformationType, implode(',', self::ALLOWED_TRANSFORMATIONS)));
        }

        $transformedPatterns = [];
        $currencyPatterns = explode(';', $currencyPattern);
        foreach ($currencyPatterns as $pattern) {
            $transformedPatterns[] = $this->transformPattern($pattern, $transformationType);
        }

        return implode(';', $transformedPatterns);
    }

    /**
     * @param string $currencyPattern
     *
     * @return string
     */
    public function getTransformationType(string $currencyPattern)
    {
        $patterns = explode(';', $currencyPattern);
        $pattern = str_replace(self::RTL_CHARACTER, '', $patterns[0]);

        $regexpList = [
            self::TYPE_LEFT_SYMBOL_WITH_SPACE => '/^¤[ ' . self::NO_BREAK_SPACE . ']+.+/',
            self::TYPE_LEFT_SYMBOL_WITHOUT_SPACE => '/^¤[^ ' . self::NO_BREAK_SPACE . ']+/',
            self::TYPE_RIGHT_SYMBOL_WITH_SPACE => '/.+[ ' . self::NO_BREAK_SPACE . ']+¤$/',
            self::TYPE_RIGHT_SYMBOL_WITHOUT_SPACE => '/[^ ' . self::NO_BREAK_SPACE . ']+¤$/',
        ];
        foreach ($regexpList as $type => $regexp) {
            if (preg_match($regexp, $pattern)) {
                return $type;
            }
        }

        return '';
    }

    /**
     * @param string $basePattern
     * @param string $transformationType
     *
     * @return string
     */
    private function transformPattern(string $basePattern, string $transformationType)
    {
        $rtlCharacter = $this->getRtlCharacter($basePattern);
        $trimmedPattern = trim($basePattern, self::CHARACTERS_TO_TRIM);

        return strtr(
            self::TRANSFORM_DICTIONARY[$transformationType],
            [
                '$rtl' => $rtlCharacter,
                '$currencySymbol' => self::CURRENCY_SYMBOL,
                '$nbsp' => self::NO_BREAK_SPACE,
                '$pattern' => $trimmedPattern,
            ]
        );
    }

    /**
     * @param string $currencyPattern
     *
     * @return string
     */
    private function getRtlCharacter(string $currencyPattern): string
    {
        return (false !== strpos($currencyPattern, self::RTL_CHARACTER)) ? self::RTL_CHARACTER : '';
    }
}
