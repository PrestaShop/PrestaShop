<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Product\Query;

use PrestaShop\PrestaShop\Core\Domain\Currency\Exception\CurrencyConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Currency\ValueObject\AlphaIsoCode;
use PrestaShop\PrestaShop\Core\Domain\Order\Exception\OrderException;
use PrestaShop\PrestaShop\Core\Domain\Order\ValueObject\OrderId;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductSearchEmptyPhraseException;

/**
 * Queries for products by provided search phrase
 */
class SearchProducts
{
    /**
     * @var string
     */
    private $phrase;

    /**
     * @var int
     */
    private $resultsLimit;

    /**
     * @var AlphaIsoCode
     */
    private $alphaIsoCode;

    /**
     * @var OrderId|null
     */
    private $orderId;

    /**
     * @param string $phrase
     * @param int $resultsLimit
     * @param string $isoCode
     * @param int|null $orderId
     *
     * @throws ProductSearchEmptyPhraseException
     * @throws CurrencyConstraintException
     */
    public function __construct(
        string $phrase,
        int $resultsLimit,
        string $isoCode,
        ?int $orderId = null
    ) {
        $this->assertIsNotEmptyString($phrase);
        $this->phrase = $phrase;
        $this->resultsLimit = $resultsLimit;
        $this->alphaIsoCode = new AlphaIsoCode($isoCode);
        if (null !== $orderId) {
            $this->setOrderId($orderId);
        }
    }

    /**
     * @return AlphaIsoCode
     */
    public function getAlphaIsoCode(): AlphaIsoCode
    {
        return $this->alphaIsoCode;
    }

    /**
     * @return string
     */
    public function getPhrase()
    {
        return $this->phrase;
    }

    /**
     * @return int
     */
    public function getResultsLimit(): int
    {
        return $this->resultsLimit;
    }

    /**
     * @return OrderId|null
     */
    public function getOrderId(): ?OrderId
    {
        return $this->orderId;
    }

    /**
     * @param int $orderId
     *
     * @throws OrderException
     */
    private function setOrderId(int $orderId): void
    {
        $this->orderId = new OrderId($orderId);
    }

    /**
     * @param string $phrase
     *
     * @throws ProductSearchEmptyPhraseException
     */
    private function assertIsNotEmptyString(string $phrase): void
    {
        if ($phrase === '') {
            throw new ProductSearchEmptyPhraseException('Product search phrase must be a not empty string');
        }
    }
}
