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

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Result;
use Doctrine\ORM\EntityManager;
use PHPUnit\Framework\TestCase;
use PrestaShopBundle\Command\UpdateSchemaCommand;

class UpdateSchemaCommandTest extends TestCase
{
    /**
     * @var UpdateSchemaCommand
     */
    protected $command;

    protected function setUp(): void
    {
        $manager = $this
            ->getMockBuilder(EntityManager::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->command = new UpdateSchemaCommand(
            'prestashop',
            'ps',
            $manager
        );
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

        $this->command->removeDuplicateDropForeignKeys(
            $queries
        );

        $this->assertEquals(
            [
                'ALTER TABLE `ps_attribute_lang` DROP FOREIGN KEY `FK_3ABE46A7BA299860`',
                2 => 'ALTER TABLE `ps_attribute` DROP FOREIGN KEY `FK_3ABE46A776283763`',
            ],
            $queries
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

    /**
     * @dataProvider getQueriesForClear
     */
    public function testClearQueries(string $query, string $expected): void
    {
        $queries = [$query];

        $connection = $this
            ->getMockBuilder(Connection::class)
            ->disableOriginalConstructor()
            ->setMethods(
                [
                    'executeQuery',
                ]
            )
            ->getMock();

        $connection
            ->expects($this->any())
            ->method('executeQuery')
            ->will($this->returnCallback([$this, 'columnsNames']));

        $this->command->clearQueries($connection, $queries);
        $this->assertEquals(
            [
                $expected,
            ],
            $queries
        );
    }

    public function columnsNames(string $name): Result
    {
        $data = [
            'SHOW FULL COLUMNS FROM ps_pa_subcontractor WHERE Field="cutting_price"' => [
                [
                    'Default' => '0',
                    'Extra' => '',
                    'Null' => 'NO',
                ],
            ],
            'SHOW FULL COLUMNS FROM ps_pa_fixation WHERE Field="resistance"' => [
                [
                    'Default' => '0',
                    'Extra' => '',
                    'Null' => 'YES',
                ],
            ],
            'SHOW FULL COLUMNS FROM ps_pa_form WHERE Field="active"' => [
                [
                    'Default' => '0',
                    'Extra' => '',
                    'Null' => 'NO',
                ],
            ],
            'SHOW FULL COLUMNS FROM ps_pa_subcontractor_material WHERE Field="labor_cost"' => [
                [
                    'Default' => '0',
                    'Extra' => '',
                    'Null' => 'NO',
                ],
            ],
            'SHOW FULL COLUMNS FROM ps_pa_structure WHERE Field="position"' => [
                [
                    'Default' => '0',
                    'Extra' => '',
                    'Null' => 'NO',
                ],
            ],
            'SHOW FULL COLUMNS FROM ps_pa_nettype WHERE Field="active"' => [
                [
                    'Default' => '0',
                    'Extra' => '',
                    'Null' => 'NO',
                ],
            ],
            'SHOW FULL COLUMNS FROM ps_pa_answer WHERE Field="nright"' => [
                [
                    'Default' => '0',
                    'Extra' => '',
                    'Null' => 'NO',
                ],
            ],
            'SHOW FULL COLUMNS FROM ps_pa_quotation_net WHERE Field="id_employee"' => [
                [
                    'Default' => '0',
                    'Extra' => '',
                    'Null' => 'NO',
                ],
            ],
            'SHOW FULL COLUMNS FROM ps_pa_quotation_net WHERE Field="dimensions"' => [
                [
                    'Default' => '0',
                    'Extra' => '',
                    'Null' => 'YES',
                ],
            ],
            'SHOW FULL COLUMNS FROM ps_pa_quotation_net WHERE Field="mirror"' => [
                [
                    'Default' => null,
                    'Extra' => '',
                    'Null' => '',
                ],
            ],
            'SHOW FULL COLUMNS FROM ps_pa_quotation_net WHERE Field="is_complete"' => [
                [
                    // Empty data, no result
                ],
            ],
        ];

        $result = $this
            ->getMockBuilder(Result::class)
            ->disableOriginalConstructor()
            ->setMethods(
                [
                    'fetchAllAssociative',
                ]
            )
            ->getMockForAbstractClass();

        $result
            ->expects($this->any())
            ->method('fetchAllAssociative')
            ->willReturn($data[$name]);

        return $result;
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

    public function getQueriesForClear(): array
    {
        return [
            [
                'UNKNOW SQL COMMAND',
                'UNKNOW SQL COMMAND',
            ],
            [
                'ALTER TABLE ps_check MISSING SQL',
                'ALTER TABLE ps_check MISSING SQL',
            ],
            [
                'ALTER TABLE ps_pa_subcontractor CHANGE cutting_price cutting_price NUMERIC(20, 6) NOT NULL',
                'ALTER TABLE ps_pa_subcontractor CHANGE cutting_price cutting_price NUMERIC(20, 6) NOT NULL DEFAULT \'0\' ',
            ],
            [
                'ALTER TABLE ps_pa_fixation CHANGE resistance resistance LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', CHANGE position position INT UNSIGNED DEFAULT 0 NOT NULL',
                'ALTER TABLE ps_pa_fixation CHANGE resistance resistance LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', CHANGE position position INT UNSIGNED DEFAULT 0 NOT NULL',
            ],
            [
                'ALTER TABLE ps_pa_form CHANGE active active TINYINT(1) DEFAULT \'0\' NOT NULL',
                'ALTER TABLE ps_pa_form CHANGE active active TINYINT(1) DEFAULT \'0\' ',
            ],
            [
                'ALTER TABLE ps_pa_subcontractor_material CHANGE labor_cost labor_cost NUMERIC(20, 6) NOT NULL',
                'ALTER TABLE ps_pa_subcontractor_material CHANGE labor_cost labor_cost NUMERIC(20, 6) NOT NULL DEFAULT \'0\' ',
            ],
            [
                'ALTER TABLE ps_pa_structure CHANGE position position INT UNSIGNED DEFAULT 0 NOT NULL',
                'ALTER TABLE ps_pa_structure CHANGE position position INT UNSIGNED DEFAULT \'0\' ',
            ],
            [
                'ALTER TABLE ps_pa_nettype CHANGE active active TINYINT(1) DEFAULT \'0\' NOT NULL',
                'ALTER TABLE ps_pa_nettype CHANGE active active TINYINT(1) DEFAULT \'0\' ',
            ],
            [
                'ALTER TABLE ps_pa_answer CHANGE nright nright INT NOT NULL',
                'ALTER TABLE ps_pa_answer CHANGE nright nright INT NOT NULL DEFAULT \'0\' ',
            ],
            [
                'ALTER TABLE ps_pa_quotation_net CHANGE id_employee id_employee INT NOT NULL, CHANGE uuid uuid VARCHAR(36) NOT NULL, CHANGE dimensions dimensions LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', CHANGE plan_dimensions plan_dimensions LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', CHANGE mirror mirror TINYINT(1) NOT NULL, CHANGE net_cost_price net_cost_price NUMERIC(20, 6) NOT NULL, CHANGE is_complete is_complete TINYINT(1) NOT NULL',
                'ALTER TABLE ps_pa_quotation_net CHANGE id_employee id_employee INT NOT NULL DEFAULT \'0\' , CHANGE uuid uuid VARCHAR(36) NOT NULL, CHANGE dimensions dimensions LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', CHANGE plan_dimensions plan_dimensions LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', CHANGE mirror mirror TINYINT(1) NOT NULL, CHANGE net_cost_price net_cost_price NUMERIC(20, 6) NOT NULL, CHANGE is_complete is_complete TINYINT(1) NOT NULL',
            ],
        ];
    }
}
