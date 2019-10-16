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
    const NO_BREAK_SPACE = ' ';
    const REGULAR_SPACE = ' ';
    const CURRENCY_SYMBOL = '¤';

    const TYPE_LEFT_SYMBOL_WITH_SPACE = 'leftWithSpace';
    const TYPE_LEFT_SYMBOL_WITHOUT_SPACE = 'leftWithoutSpace';
    const TYPE_RIGHT_SYMBOL_WITH_SPACE = 'rightWithSpace';
    const TYPE_RIGHT_SYMBOL_WITHOUT_SPACE = 'rightWithoutSpace';

    /**
     * @var string
     */
    private $currencyPattern;

    /**
     * @var string
     */
    private $trimmedPattern;

    /**
     * @param string $currencyPattern Initial currency pattern (ex: #,##0.00¤, ¤#,##,##0.00)
     */
    public function __construct(string $currencyPattern)
    {
        $this->currencyPattern = $currencyPattern;

        $trimmedCharacters = implode('', [self::CURRENCY_SYMBOL, self::NO_BREAK_SPACE, self::REGULAR_SPACE]);
        $this->trimmedPattern = trim($currencyPattern, $trimmedCharacters);
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
        $allowedTransformations = [
            self::TYPE_LEFT_SYMBOL_WITH_SPACE,
            self::TYPE_LEFT_SYMBOL_WITHOUT_SPACE,
            self::TYPE_RIGHT_SYMBOL_WITH_SPACE,
            self::TYPE_RIGHT_SYMBOL_WITHOUT_SPACE,
        ];

        if (!in_array($transformationType, $allowedTransformations)) {
            throw new InvalidArgumentException(sprintf(
                'Invalid transformation type "%s", allowed transformations are: %s',
                $transformationType,
                implode(',', $allowedTransformations)
            ));
        }

        switch ($transformationType) {
            case self::TYPE_LEFT_SYMBOL_WITH_SPACE:
                return self::CURRENCY_SYMBOL . self::NO_BREAK_SPACE . $this->trimmedPattern;
                break;
            case self::TYPE_LEFT_SYMBOL_WITHOUT_SPACE:
                return self::CURRENCY_SYMBOL . $this->trimmedPattern;
                break;
            case self::TYPE_RIGHT_SYMBOL_WITH_SPACE:
                return $this->trimmedPattern . self::NO_BREAK_SPACE . self::CURRENCY_SYMBOL;
                break;
            case self::TYPE_RIGHT_SYMBOL_WITHOUT_SPACE:
                return $this->trimmedPattern . self::CURRENCY_SYMBOL;
                break;
        }

        return $this->currencyPattern;
    }
}
