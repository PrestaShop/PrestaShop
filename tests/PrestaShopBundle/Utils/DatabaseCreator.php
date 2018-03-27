<?php
/**
 * 2007-2018 PrestaShop
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace Tests\PrestaShopBundle\Utils;

use Doctrine\DBAL\DBALException;
use PrestaShopBundle\Install\DatabaseDump;
use PrestaShopBundle\Install\Install;
use Symfony\Component\Process\Process;

class DatabaseCreator
{
    /**
     * Create the initialize database used for test
     */
    public static function createTestDB()
    {
        define('_PS_IN_TEST_', true);
        define('__PS_BASE_URI__', '/');
        define('_PS_ROOT_DIR_', __DIR__ . '/../../..');
        define('_PS_MODULE_DIR_', _PS_ROOT_DIR_ . '/tests/resources/modules/');
        require_once(__DIR__ . '/../../../install-dev/init.php');

        $install = new Install();
        \DbPDOCore::createDatabase(_DB_SERVER_, _DB_USER_, _DB_PASSWD_, _DB_NAME_, false);
        $install->clearDatabase(false);
        $install->installDatabase(true);
        $process = new Process('php bin/console prestashop:schema:update-without-foreign --env=test');
        $process->run();
        $install->initializeTestContext();
        $install->installDefaultData('test_shop', false, false, false);
        $install->populateDatabase();
        $install->installCldrDatas();

        $install->configureShop(array(
            'admin_firstname' => 'puff',
            'admin_lastname' => 'daddy',
            'admin_password' => 'test',
            'admin_email' => 'test@prestashop.com',
            'configuration_agrement' => true,
            'send_informations' => false,
        ));
        $install->installFixtures();
        $install->installTheme();
        $language = new \Language(1);
        \Context::getContext()->language = $language;
        $install->installModules();
        $install->installModulesAddons();

        DatabaseDump::create();
    }

    /**
     * Restore the test database in its initial state from a dump generated during createTestDB
     *
     * @throws DBALException
     */
    public static function restoreTestDB()
    {
        if (!file_exists(sys_get_temp_dir() . '/' . 'ps_dump.sql')) {
            throw new DBALException('You need to run \'composer create-test-db\' to create the initial test database');
        }

        DatabaseDump::restoreDb();
    }
}
