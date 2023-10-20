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

namespace PrestaShop\PrestaShop\Core\Domain\Currency\QueryResult;

class ReferenceCurrency
{
    /**
     * @var string[]
     */
    private $names;

    /**
     * @var string[]
     */
    private $symbols;

    /**
     * @var string[]
     */
    private $patterns;

    /**
     * @var string
     */
    private $isoCode;

    /**
     * @var string|null
     */
    private $numericIsoCode;

    /**
     * @var int
     */
    private $precision;

    /**
     * @param string $isoCode
     * @param string $numericIsoCode
     * @param array $names
     * @param array $symbols
     * @param array $patterns
     * @param int $precision
     */
    public function __construct(
        string $isoCode,
        string $numericIsoCode,
        array $names,
        array $symbols,
        array $patterns,
        int $precision
    ) {
        $this->isoCode = $isoCode;
        $this->numericIsoCode = $numericIsoCode;
        $this->names = $names;
        $this->symbols = $symbols;
        $this->patterns = $patterns;
        $this->precision = $precision;
    }

    /**
     * Currency ISO code
     *
     * @return string
     */
    public function getIsoCode(): string
    {
        return $this->isoCode;
    }

    /**
     * Currency numeric ISO code
     *
     * @return string
     */
    public function getNumericIsoCode(): string
    {
        return $this->numericIsoCode;
    }

    /**
     * Currency's names, indexed by language id.
     *
     * @return array
     */
    public function getNames(): array
    {
        return $this->names;
    }

    /**
     * Currency's names, indexed by language id.
     *
     * @return array
     */
    public function getSymbols(): array
    {
        return $this->symbols;
    }

    /**
     * Currency's patterns, indexed by language id.
     *
     * @return array
     */
    public function getPatterns(): array
    {
        return $this->patterns;
    }

    /**
     * Currency decimal precision
     *
     * @return int
     */
    public function getPrecision(): int
    {
        return $this->precision;
    }
}
