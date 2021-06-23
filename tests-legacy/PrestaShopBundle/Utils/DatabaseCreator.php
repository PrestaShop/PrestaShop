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

namespace LegacyTests\PrestaShopBundle\Utils;

use Context;
use Doctrine\DBAL\DBALException;
use PrestaShopBundle\Install\DatabaseDump;
use PrestaShopBundle\Install\Install;
use Tests\Resources\ResourceResetter;
use Tab;

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
        define('_PS_MODULE_DIR_', _PS_ROOT_DIR_ . '/modules/');
        require_once __DIR__ . '/../../../install-dev/init.php';

        $install = new Install();
        $install->setTranslator(Context::getContext()->getTranslatorFromLocale('en'));
        \DbPDOCore::createDatabase(_DB_SERVER_, _DB_USER_, _DB_PASSWD_, _DB_NAME_, false);
        $install->clearDatabase(false);
        if (!$install->installDatabase(true)) {
            // Something went wrong during installation
            exit(1);
        }

        $install->initializeTestContext();
        $install->installDefaultData('test_shop', false, false, false);
        $install->populateDatabase();

        $install->configureShop(array(
            'admin_firstname' => 'puff',
            'admin_lastname' => 'daddy',
            'admin_password' => 'test',
            'admin_email' => 'test@prestashop.com',
            'configuration_agrement' => true,
        ));
        $install->installFixtures();
        Tab::resetStaticCache();
        $install->installTheme();
        $install->installModules();

        DatabaseDump::create();

        $resourceResetter = new ResourceResetter();
        $resourceResetter->backupImages();
        $resourceResetter->backupDownloads();
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
