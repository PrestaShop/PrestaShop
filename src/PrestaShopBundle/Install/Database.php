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

namespace PrestaShopBundle\Install;

use PrestaShop\PrestaShop\Adapter\Entity\Db;
use PrestaShop\PrestaShop\Adapter\Entity\Validate;

class Database extends AbstractInstall
{
    /**
     * Check database configuration and try a connection.
     *
     * @param string $server
     * @param string $database
     * @param string $login
     * @param string $password
     * @param string $prefix
     * @param bool $clear
     *
     * @return array List of errors
     */
    public function testDatabaseSettings($server, $database, $login, $password, $prefix, $clear = false)
    {
        $errors = [];

        // Check if fields are correctly typed
        if (!$server || !Validate::isUrl($server)) {
            $errors[] = $this->translator->trans('Server name is not valid', [], 'Install');
        }

        if (!$database) {
            $errors[] = $this->translator->trans('You must enter a database name', [], 'Install');
        }

        if (!$login) {
            $errors[] = $this->translator->trans('You must enter a database login', [], 'Install');
        }

        if ($prefix && !Validate::isTablePrefix($prefix)) {
            $errors[] = $this->translator->trans('Tables prefix is invalid', [], 'Install');
        }

        if (!$errors) {
            $dbtype = ' (' . Db::getClass() . ')';
            // Try to connect to database
            switch (Db::checkConnection($server, $login, $password, $database, true)) {
                case 0:
                    if (!Db::checkEncoding($server, $login, $password)) {
                        $errors[] = $this->translator->trans('Cannot convert database data to utf-8', [], 'Install') . $dbtype;
                    }

                    // Check if a table with same prefix already exists
                    if (!$clear && Db::hasTableWithSamePrefix($server, $login, $password, $database, $prefix)) {
                        $errors[] = $this->translator->trans('At least one table with same prefix was already found, please change your prefix or drop your database', [], 'Install');
                    }
                    if (!Db::checkAutoIncrement($server, $login, $password)) {
                        $errors[] = $this->translator->trans('The values of auto_increment increment and offset must be set to 1', [], 'Install');
                    }
                    if (($create_error = Db::checkCreatePrivilege($server, $login, $password, $database, $prefix)) !== true) {
                        $errors[] = $this->translator->trans('Your database login does not have the privileges to create table on the database "%s". Ask your hosting provider:', ['%database%' => $database], 'Install');
                        if ($create_error != false) {
                            $errors[] = $create_error;
                        }
                    }

                    break;

                case 1:
                    $errors[] = $this->translator->trans('Database Server is not found. Please verify the login, password and server fields', [], 'Install') . $dbtype;

                    break;

                case 2:
                    $error = $this->translator->trans('Connection to MySQL server succeeded, but database "%database%" not found', ['%database%' => $database], 'Install') . $dbtype;
                    if ($this->createDatabase($server, $database, $login, $password, true)) {
                        $error .= '<p>' . sprintf('<input type="button" value="%s" class="button" id="btCreateDB">', $this->translator->trans('Attempt to create the database automatically', [], 'Install')) . '</p>
						<script type="text/javascript">bindCreateDB();</script>';
                    }
                    $errors[] = $error;

                    break;
            }
        }

        if (count($errors)) {
            $this->setError($errors);
        }

        return $errors;
    }

    public function createDatabase($server, $database, $login, $password, $dropit = false)
    {
        $class = '\\' . Db::getClass();

        return call_user_func([$class, 'createDatabase'], $server, $login, $password, $database, $dropit);
    }

    public function getBestEngine($server, $database, $login, $password)
    {
        $class = '\\' . Db::getClass();
        $instance = new $class($server, $login, $password, $database, true);
        $engine = $instance->getBestEngine();
        unset($instance);

        return $engine;
    }
}
