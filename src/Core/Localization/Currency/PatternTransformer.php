<?php
/**
 * 2007-2019 PrestaShop SA and Contributors
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

namespace PrestaShop\PrestaShop\Core\Localization\Currency;

use PrestaShop\PrestaShop\Core\Exception\InvalidArgumentException;

/**
 * Transform a currency pattern by moving the symbol position, with or without
 * a separation space (no-break space).
 */
class PatternTransformer
{
    const NO_BREAK_SPACE = "\u{00A0}";
    const REGULAR_SPACE = ' ';
    const CURRENCY_SYMBOL = '¤';

    const TYPE_LEFT_SYMBOL_WITH_SPACE = 'leftWithSpace';
    const TYPE_LEFT_SYMBOL_WITHOUT_SPACE = 'leftWithoutSpace';
    const TYPE_RIGHT_SYMBOL_WITH_SPACE = 'rightWithSpace';
    const TYPE_RIGHT_SYMBOL_WITHOUT_SPACE = 'rightWithoutSpace';

    const ALLOWED_TRANSFORMATIONS = [
        self::TYPE_LEFT_SYMBOL_WITH_SPACE,
        self::TYPE_LEFT_SYMBOL_WITHOUT_SPACE,
        self::TYPE_RIGHT_SYMBOL_WITH_SPACE,
        self::TYPE_RIGHT_SYMBOL_WITHOUT_SPACE,
    ];

    const TRIMMED_CHARACTERS = [
        self::CURRENCY_SYMBOL,
        self::NO_BREAK_SPACE,
        self::REGULAR_SPACE,
    ];

    /**
     * @var string
     */
    private $currencyPattern;

    /**
     * @var string[]
     */
    private $trimmedPatterns;

    /**
     * @param string $currencyPattern Initial currency pattern (ex: #,##0.00¤, ¤#,##,##0.00, ¤#,##0.00;¤-#,##0.00)
     */
    public function __construct(string $currencyPattern)
    {
        $this->setCurrencyPattern($currencyPattern);
    }

    /**
     * @param string $transformationType
     *
     * @return string
     *
     * @throws InvalidArgumentException
     */
    public function transform(string $transformationType): string
    {
        if (!in_array($transformationType, self::ALLOWED_TRANSFORMATIONS)) {
            throw new InvalidArgumentException(sprintf(
                'Invalid transformation type "%s", allowed transformations are: %s',
                $transformationType,
                implode(',', self::ALLOWED_TRANSFORMATIONS)
            ));
        }

        $transformedPatterns = [];
        foreach ($this->trimmedPatterns as $trimmedPattern) {
            $transformedPatterns[] = $this->transformPattern($trimmedPattern, $transformationType);
        }

        return implode(';', $transformedPatterns);
    }

    /**
     * @param string $pattern
     * @param string $transformationType
     *
     * @return string
     */
    private function transformPattern(string $pattern, string $transformationType)
    {
        switch ($transformationType) {
            case self::TYPE_LEFT_SYMBOL_WITH_SPACE:
                return self::CURRENCY_SYMBOL . self::NO_BREAK_SPACE . $pattern;
            case self::TYPE_LEFT_SYMBOL_WITHOUT_SPACE:
                return self::CURRENCY_SYMBOL . $pattern;
            case self::TYPE_RIGHT_SYMBOL_WITH_SPACE:
                return $pattern . self::NO_BREAK_SPACE . self::CURRENCY_SYMBOL;
            case self::TYPE_RIGHT_SYMBOL_WITHOUT_SPACE:
                return $pattern . self::CURRENCY_SYMBOL;
        }
    }

    /**
     * @return string
     */
    public function getCurrencyPattern(): string
    {
        return $this->currencyPattern;
    }

    /**
     * @param string $currencyPattern
     *
     * @return PatternTransformer
     */
    public function setCurrencyPattern(string $currencyPattern): PatternTransformer
    {
        $this->currencyPattern = $currencyPattern;

        $currencyPatterns = explode(';', $this->currencyPattern);
        $this->trimmedPatterns = [];
        foreach ($currencyPatterns as $pattern) {
            $trimmedCharacters = implode('', self::TRIMMED_CHARACTERS);
            $this->trimmedPatterns[] = trim($pattern, $trimmedCharacters);
        }

        return $this;
    }
}
