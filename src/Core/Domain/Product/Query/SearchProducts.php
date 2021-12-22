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
