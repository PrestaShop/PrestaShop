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

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Product\Query;

/**
 * Searches products to add a related product
 */
class SearchProductsToRelate
{
    /**
     * Default search results limit
     */
    const LIMIT_DEFAULT = 10;

    /**
     * @var string
     */
    private $phrase;

    /**
     * @var int
     */
    private $limit;

    /**
     * @param string $phrase
     * @param int $limit
     */
    public function __construct(
        string $phrase,
        int $limit = self::LIMIT_DEFAULT
    ) {
        $this->phrase = $phrase;
        $this->limit = $limit;
    }

    /**
     * @return string
     */
    public function getPhrase(): string
    {
        return $this->phrase;
    }

    /**
     * @return int
     */
    public function getLimit(): int
    {
        return $this->limit;
    }
}
