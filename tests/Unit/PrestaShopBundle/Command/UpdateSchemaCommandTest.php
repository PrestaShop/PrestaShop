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

namespace Tests\Unit\PrestaShopBundle\Command;

use PHPUnit\Framework\TestCase;
use PrestaShopBundle\Command\UpdateSchemaCommand;

class UpdateSchemaCommandTest extends TestCase
{
    /**
     * @var UpdateSchemaCommand
     */
    protected $command;

    protected function setUp()
    {
        $this->command = new UpdateSchemaCommand();
    }

    public function testRemoveDropTables(): void
    {
        $queries = $this->getQueries();
        $tablesDropped = $this->command->removeDropTables($queries);

        $this->assertEquals(
            [
                'ps_blockwishlist_statistics',
                'ps_attribute',
                'ps_layered_indexable_attribute_group',
            ],
            $tablesDropped
        );
    }

    public function testRemoveAlterTables(): void
    {
        $queries = $this->getQueries();
        $this->command->removeAlterTables(
            $queries,
            [
                'ps_blockwishlist_statistics',
                'ps_attribute',
                'ps_layered_indexable_attribute_group',
            ]
        );

        $this->assertEquals(
            [
                'DROP TABLE ps_blockwishlist_statistics',
                'DROP TABLE ps_attribute',
                'DROP TABLE ps_layered_indexable_attribute_group',
                4 => 'ALTER TABLE ps_attribute_lang ADD CONSTRAINT FK_3ABE46A7BA299860 FOREIGN KEY (id_lang) REFERENCES ps_lang (id_lang) ON DELETE CASCADE',
                'ALTER TABLE `ps_attribute_lang` DROP FOREIGN KEY `FK_3ABE46A7BA299860`',
            ],
            $queries
        );
    }

    public function testRemoveDropForeignKeys(): void
    {
        $queries = [
            'ALTER TABLE `ps_attribute_lang` DROP FOREIGN KEY `FK_3ABE46A7BA299860`',
            'ALTER TABLE `ps_attribute_lang` DROP FOREIGN KEY `FK_3ABE46A7BA299860`',
            'ALTER TABLE `ps_attribute` DROP FOREIGN KEY `FK_3ABE46A776283763`',
        ];

        $duplicateQueries = $this->command->removeDuplicateDropForeignKeys(
            $queries
        );

        $this->assertEquals(
            [
                'ALTER TABLE `ps_attribute_lang` DROP FOREIGN KEY `FK_3ABE46A7BA299860`',
                2 => 'ALTER TABLE `ps_attribute` DROP FOREIGN KEY `FK_3ABE46A776283763`',
            ],
            $queries
        );
        $this->assertEquals(
            [],
            $duplicateQueries
        );
    }

    public function testRemoveAddConstraints(): void
    {
        $queries = $this->getQueries();
        $this->command->removeAddConstraints(
            $queries
        );

        $this->assertEquals(
            [
                'DROP TABLE ps_blockwishlist_statistics',
                'DROP TABLE ps_attribute',
                'DROP TABLE ps_layered_indexable_attribute_group',
                5 => 'ALTER TABLE `ps_attribute_lang` DROP FOREIGN KEY `FK_3ABE46A7BA299860`',
            ],
            $queries
        );
    }

    public function testMoveConstraints(): void
    {
        $queries = $this->getQueries();
        $this->command->moveConstraints(
            $queries
        );

        $this->assertEquals(
            [
                'ALTER TABLE `ps_attribute_lang` DROP FOREIGN KEY `FK_3ABE46A7BA299860`',
                'DROP TABLE ps_blockwishlist_statistics',
                'DROP TABLE ps_attribute',
                'DROP TABLE ps_layered_indexable_attribute_group',
                'ALTER TABLE ps_attribute ADD CONSTRAINT FK_6C3355F967A664FB FOREIGN KEY (id_attribute_group) REFERENCES ps_attribute_group (id_attribute_group)',
                'ALTER TABLE ps_attribute_lang ADD CONSTRAINT FK_3ABE46A7BA299860 FOREIGN KEY (id_lang) REFERENCES ps_lang (id_lang) ON DELETE CASCADE',
            ],
            $queries
        );
    }

    public function testClearQueries(): void
    {
        $queries = $this->getQueries();
    }

    public function getQueries(): array
    {
        return [
            'DROP TABLE ps_blockwishlist_statistics',
            'DROP TABLE ps_attribute',
            'DROP TABLE ps_layered_indexable_attribute_group',
            'ALTER TABLE ps_attribute ADD CONSTRAINT FK_6C3355F967A664FB FOREIGN KEY (id_attribute_group) REFERENCES ps_attribute_group (id_attribute_group)',
            'ALTER TABLE ps_attribute_lang ADD CONSTRAINT FK_3ABE46A7BA299860 FOREIGN KEY (id_lang) REFERENCES ps_lang (id_lang) ON DELETE CASCADE',
            'ALTER TABLE `ps_attribute_lang` DROP FOREIGN KEY `FK_3ABE46A7BA299860`',
        ];
    }
}
