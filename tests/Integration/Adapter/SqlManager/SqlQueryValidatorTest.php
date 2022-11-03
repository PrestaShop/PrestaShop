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

declare(strict_types=1);

namespace Tests\Integration\Adapter\SqlManager;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\SqlManager\SqlQueryValidator;

class SqlQueryValidatorTest extends TestCase
{
    /**
     * @dataProvider dataProviderValidate
     *
     * @param string $sql
     * @param array $errors
     */
    public function testValidate(string $sql, array $errors): void
    {
        $validator = new SqlQueryValidator();
        self::assertEquals($errors, $validator->validate($sql));
    }

    public function dataProviderValidate(): iterable
    {
        // Valid queries
        yield [
            'SELECT * FROM ps_customer',
            [],
        ];
        yield [
            'SELECT MAX(o.date_add), COUNT(o.id_order), SUM(o.total_paid_tax_incl), c.id_customer, c.firstname, c.lastname, c.email, c.last_passwd_gen, c.newsletter_date_add, c.date_add, c.date_upd '
            . 'FROM ps_customer c '
            . 'LEFT JOIN ps_orders o ON c.id_customer = o.id_customer '
            . 'GROUP BY c.id_customer;',
            [],
        ];
        yield [
            'SELECT SQL_CALC_FOUND_ROWS b.*, a.* '
            . 'FROM `ps_cart_rule` a '
            . 'LEFT JOIN `ps_cart_rule_lang` b ON (b.`id_cart_rule` = a.`id_cart_rule` AND b.`id_lang` = 1) '
            . 'WHERE 1 '
            . 'ORDER BY a.id_cart_rule DESC',
            [],
        ];
        yield [
            'SELECT SQL_CALC_FOUND_ROWS b.*, a.* '
            . 'FROM `ps_cart_rule` a '
            . 'LEFT JOIN `ps_cart_rule_lang` b ON (b.`id_cart_rule` = a.`id_cart_rule` AND b.`id_lang` = a.`id_cart_rule`) '
            . 'WHERE 1 '
            . 'ORDER BY a.id_cart_rule DESC',
            [],
        ];
        yield [
            'SELECT SQL_CALC_FOUND_ROWS b.*, a.* '
            . 'FROM `ps_cart_rule` a '
            . 'LEFT JOIN `ps_cart_rule_lang` b ON (b.`id_cart_rule` = a.`id_cart_rule` AND 1) '
            . 'WHERE 1 '
            . 'ORDER BY a.id_cart_rule DESC',
            [],
        ];
        // Invalid queries
        yield [
            'SELECT * FROM ps_customera',
            [
                [
                    'key' => 'The "%tablename%" table does not exist.',
                    'parameters' => [
                        '%tablename%' => 'ps_customera',
                    ],
                    'domain' => 'Admin.Advparameters.Notification',
                ],
            ],
        ];
        yield [
            'SELECT * FROM ps_customer c LEFT JOIN ps_orders o ON c.id_customer = o.id_customera',
            [
                [
                    'key' => 'The "%attribute%" attribute does not exist in the "%table%" table.',
                    'parameters' => [
                        '%attribute%' => 'id_customera',
                        '%table%' => 'ps_orders',
                    ],
                    'domain' => 'Admin.Advparameters.Notification',
                ],
            ],
        ];
        yield [
            'SELECT * FROM ps_customer c GROUP BY c.ida',
            [
                [
                    'key' => 'The "%attribute%" attribute does not exist in the "%table%" table.',
                    'parameters' => [
                        '%attribute%' => 'ida',
                        '%table%' => 'ps_customer',
                    ],
                    'domain' => 'Admin.Advparameters.Notification',
                ],
            ],
        ];
    }
}
