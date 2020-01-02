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

namespace PrestaShop\PrestaShop\Core\Domain\Product\Query;

use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductException;

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
     * Optional
     * If provided, found products will be returned using this currency
     *
     * @var int
     */
    private $currencyId;

    /**
     * @param string $phrase
     * @param int $resultsLimit
     * @param int $currencyId
     *
     * @throws ProductException
     */
    public function __construct(string $phrase, int $resultsLimit, int $currencyId = null)
    {
        $this->assertIsNotEmptyString($phrase);
        $this->phrase = $phrase;
        $this->resultsLimit = $resultsLimit;
        $this->currencyId = $currencyId;
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
     * @return int
     */
    public function getCurrencyId(): ?int
    {
        return $this->currencyId;
    }

    /**
     * @param string $phrase
     *
     * @throws ProductException
     */
    private function assertIsNotEmptyString(string $phrase): void
    {
        if (empty($phrase) || !is_string($phrase)) {
            throw new ProductException('Product search phrase must be a not empty string');
        }
    }
}
