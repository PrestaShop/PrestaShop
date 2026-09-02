<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
