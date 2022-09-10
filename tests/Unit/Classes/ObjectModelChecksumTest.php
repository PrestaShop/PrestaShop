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

namespace Tests\Unit\Classes;

use ObjectModel;
use ObjectModelChecksum;
use PHPUnit\Framework\TestCase;
use Tools;

class ObjectModelChecksumTest extends TestCase
{
    /**
     * @var ObjectModel
     */
    private $objectModel;

    /**
     * @param string $name
     * @param array $data
     * @param string $dataName
     */
    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);

        $this->objectModel = new class() extends ObjectModel {
            public $field_int;
            public $field_string;
            public $field_bool;
            public $field_date;

            public static $definition = [
                'table' => 'object_table',
                'primary' => 'id_object_table',
                'fields' => [
                    'field_int' => ['type' => ObjectModel::TYPE_INT, 'validate' => 'isNullOrUnsignedId', 'copy_post' => false],
                    'field_string' => ['type' => ObjectModel::TYPE_STRING, 'validate' => 'isGenericName', 'required' => true, 'size' => 32],
                    'field_bool' => ['type' => ObjectModel::TYPE_BOOL, 'validate' => 'isBool', 'copy_post' => false],
                    'field_date' => ['type' => ObjectModel::TYPE_DATE, 'validate' => 'isDate', 'copy_post' => false],
                ],
            ];
        };
    }

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::declareRequiredDbConstants();
    }

    /**
     * @dataProvider providerObjectModelsWithAlgo
     */
    public function testObjectModelChecksum(ObjectModel $objectModel, string $hashAlgo): void
    {
        $this->expectExceptionMessageMatches('/^(Link to database cannot be established: SQLSTATE).*$/');

        $objectModelChecksum = (new ObjectModelChecksum($hashAlgo))->generateChecksum($objectModel);

        $this->assertEquals(strlen($objectModelChecksum), $this->getAlgoHashLengt($hashAlgo));
    }

    public function providerObjectModelsWithAlgo()
    {
        $objectModelsWithAlgo = [];

        for ($i = 0; $i <= 20; ++$i) {
            $objectModel = $this->objectModel;
            $objectModel->id = rand(1, 999);
            $objectModel->field_int = rand(1, 999);
            $objectModel->field_string = Tools::passwdGen(10, Tools::PASSWORDGEN_FLAG_ALPHANUMERIC);
            $objectModel->field_bool = (bool) rand(0, 1);
            $objectModel->field_date = date('Y-m-d H:i:s');

            $objectModelsWithAlgo[] = [$objectModel, $this->genRandomHashAlgo()];
        }

        return $objectModelsWithAlgo;
    }

    private function genRandomHashAlgo(): string
    {
        $algos = hash_algos();

        return $algos[rand(0, count($algos) - 1)];
    }

    private function getAlgoHashLengt(string $hashAlgo): int
    {
        return strlen(hash($hashAlgo, 'test'));
    }

    private static function declareRequiredDbConstants(): void
    {
        if (!defined('_DB_SERVER_')) {
            define('_DB_SERVER_', 'localhost');
        }
        if (!defined('_DB_USER_')) {
            define('_DB_USER_', 'test_db_user');
        }
        if (!defined('_DB_PASSWD_')) {
            define('_DB_PASSWD_', 'test_db_password');
        }
        if (!defined('_DB_NAME_')) {
            define('_DB_NAME_', 'test_db__name');
        }
        if (!defined('_DB_PREFIX_')) {
            define('_DB_PREFIX_', 'test_db_prefix');
        }
    }
}
