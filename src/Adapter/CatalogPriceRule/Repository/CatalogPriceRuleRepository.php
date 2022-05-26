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

namespace PrestaShop\PrestaShop\Adapter\CatalogPriceRule\Repository;

use Doctrine\DBAL\Connection;
use PrestaShop\PrestaShop\Adapter\Product\SpecificPrice\Validate\SpecificPriceValidator;
use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use PrestaShop\PrestaShop\Core\Domain\Language\ValueObject\LanguageId;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;

class CatalogPriceRuleRepository
{
    /**
     * @var Connection
     */
    private $connection;

    /**
     * @var string
     */
    private $dbPrefix;

    /**
     * @param Connection $connection
     * @param string $dbPrefix
     */
    public function __construct(
        Connection $connection,
        string $dbPrefix
    ) {
        $this->connection = $connection;
        $this->dbPrefix = $dbPrefix;
    }

    /**
     *
     * @return array<int, array<string, string|null>>
     */
    public function getAll(LanguageId $langId): array {
        $qb = $this->connection->createQueryBuilder()
            ->select('spr.id_specific_price_rule, spr.name as specific_price_rule_name, currency_lang.symbol, country_lang.name as lang_name, shop.name as shop_name, group_lang.name as group_name, spr.from_quantity, spr.reduction_type, spr.reduction, spr.from, spr.to')
            ->from($this->dbPrefix . 'specific_price_rule', 'spr')
            ->leftJoin(
                'spr',
                $this->dbPrefix . 'currency_lang',
                'currency_lang',
                'spr.id_currency = currency_lang.id_currency AND currency_lang.id_lang = :langId'
            )
            ->leftJoin(
                'spr',
                $this->dbPrefix . 'shop',
                'shop',
                'spr.id_shop = shop.id_shop'
            )
            ->leftJoin(
                'spr',
                $this->dbPrefix . 'country_lang',
                'country_lang',
                'spr.id_country = country_lang.id_country AND country_lang.id_lang = :langId'
            )
            ->leftJoin(
                'spr',
                $this->dbPrefix . 'group_lang',
                'group_lang',
                'spr.id_group = group_lang.id_group AND group_lang.id_lang = :langId'
            )
            ->orderBy('id_specific_price_rule', 'asc')
            ->setParameter('langId', $langId->getValue())
        ;

        return $qb->execute()->fetchAllAssociative();
    }
}
