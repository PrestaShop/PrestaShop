<?php
/**
 * 2007-2018 PrestaShop.
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

/**
 * Class PrestaShopBackupCore.
 */
class PrestaShopBackupCore
{
    /** @var int Object id */
    public $id;

    /** @var string Last error messages */
    public $error;

    /** @var string default backup directory. */
    public static $backupDir = '/backups/';

    /** @var string custom backup directory. */
    public $customBackupDir = null;

    public $psBackupAll = true;
    public $psBackupDropTable = true;

    /**
     * Creates a new backup object.
     *
     * @param string $filename Filename of the backup file
     */
    public function __construct($filename = null)
    {
        if ($filename) {
            $this->id = $this->getRealBackupPath($filename);
        }

        $psBackupAll = Configuration::get('PS_BACKUP_ALL');
        $psBackupDropTable = Configuration::get('PS_BACKUP_DROP_TABLE');
        $this->psBackupAll = $psBackupAll !== false ? $psBackupAll : true;
        $this->psBackupDropTable = $psBackupDropTable !== false ? $psBackupDropTable : true;
    }

    /**
     * you can set a different path with that function.
     *
     * @TODO include the prefix name
     *
     * @param string $dir
     *
     * @return bool bo
     */
    public function setCustomBackupPath($dir)
    {
        $customDir = DIRECTORY_SEPARATOR . trim($dir, '/') . DIRECTORY_SEPARATOR;
        if (is_dir((defined('_PS_HOST_MODE_') ? _PS_ROOT_DIR_ : _PS_ADMIN_DIR_) . $customDir)) {
            $this->customBackupDir = $customDir;
        } else {
            return false;
        }

        return true;
    }

    /**
     * get the path to use for backup (customBackupDir if specified, or default).
     *
     * @param string $filename filename to use
     *
     * @return string full path
     */
    public function getRealBackupPath($filename = null)
    {
        $backupDir = PrestaShopBackup::getBackupPath($filename);
        if (!empty($this->customBackupDir)) {
            $backupDir = str_replace((defined('_PS_HOST_MODE_') ? _PS_ROOT_DIR_ : _PS_ADMIN_DIR_) . self::$backupDir,
                (defined('_PS_HOST_MODE_') ? _PS_ROOT_DIR_ : _PS_ADMIN_DIR_) . $this->customBackupDir, $backupDir);

            if (strrpos($backupDir, DIRECTORY_SEPARATOR)) {
                $backupDir .= DIRECTORY_SEPARATOR;
            }
        }

        return $backupDir;
    }

    /**
     * Get the full path of the backup file.
     *
     * @param string $filename prefix of the backup file (datetime will be the second part)
     *
     * @return string The full path of the backup file, or false if the backup file does not exists
     */
    public static function getBackupPath($filename = '')
    {
        $backupdir = realpath((defined('_PS_HOST_MODE_') ? _PS_ROOT_DIR_ : _PS_ADMIN_DIR_) . self::$backupDir);

        if ($backupdir === false) {
            die(Context::getContext()->getTranslator()->trans('"Backup" directory does not exist.', array(), 'Admin.Advparameters.Notification'));
        }

        // Check the realpath so we can validate the backup file is under the backup directory
        if (!empty($filename)) {
            $backupfile = realpath($backupdir . DIRECTORY_SEPARATOR . $filename);
        } else {
            $backupfile = $backupdir . DIRECTORY_SEPARATOR;
        }

        if ($backupfile === false || strncmp($backupdir, $backupfile, strlen($backupdir)) != 0) {
            die(Tools::displayError());
        }

        return $backupfile;
    }

    /**
     * Check if a backup file exist.
     *
     * @param string $filename prefix of the backup file (datetime will be the second part)
     *
     * @return bool true if backup file exist
     */
    public static function backupExist($filename)
    {
        $backupdir = realpath((defined('_PS_HOST_MODE_') ? _PS_ROOT_DIR_ : _PS_ADMIN_DIR_) . self::$backupDir);

        if ($backupdir === false) {
            die(Context::getContext()->getTranslator()->trans('"Backup" directory does not exist.', array(), 'Admin.Advparameters.Notification'));
        }

        return @filemtime($backupdir . DIRECTORY_SEPARATOR . $filename);
    }

    /**
     * Get the URL used to retrieve this backup file.
     *
     * @return string The url used to request the backup file
     *
     * @deprecated As the call has been duplicated in the new Controller. Get the URL from the router instead.
     */
    public function getBackupURL()
    {
        // Additionnal parameters (action, filename, ajax) are kept for backward compatibility, in case we disable the new controller
        return Context::getContext()->link->getAdminLink('AdminBackup', true, ['route' => 'admin_backup_download', 'downloadFileName' => basename($this->id)]) . '&action=backupContent&ajax=1&filename=' . basename($this->id);
    }

    /**
     * Delete the current backup file.
     *
     * @return bool Deletion result, true on success
     */
    public function delete()
    {
        if (!$this->id || !unlink($this->id)) {
            $this->error = Context::getContext()->getTranslator()->trans('Error deleting', array(), 'Admin.Advparameters.Notification') . ' ' . ($this->id ? '"' . $this->id . '"' :
                Context::getContext()->getTranslator()->trans('Invalid ID', array(), 'Admin.Advparameters.Notification'));

            return false;
        }

        return true;
    }

    /**
     * Deletes a range of backup files.
     *
     * @return bool True on success
     */
    public function deleteSelection($list)
    {
        foreach ($list as $file) {
            $backup = new PrestaShopBackup($file);
            if (!$backup->delete()) {
                $this->error = $backup->error;

                return false;
            }
        }

        return true;
    }

    /**
     * Creates a new backup file.
     *
     * @return bool true on successful backup
     */
    public function add()
    {
        if (!$this->psBackupAll) {
            $ignoreInsertTable = array(_DB_PREFIX_ . 'connections', _DB_PREFIX_ . 'connections_page', _DB_PREFIX_
                . 'connections_source', _DB_PREFIX_ . 'guest', _DB_PREFIX_ . 'statssearch', );
        } else {
            $ignoreInsertTable = array();
        }

        // Generate some random number, to make it extra hard to guess backup file names
        $rand = dechex(mt_rand(0, min(0xffffffff, mt_getrandmax())));
        $date = time();
        $backupfile = $this->getRealBackupPath() . $date . '-' . $rand . '.sql';

        // Figure out what compression is available and open the file
        if (function_exists('bzopen')) {
            $backupfile .= '.bz2';
            $fp = @bzopen($backupfile, 'w');
        } elseif (function_exists('gzopen')) {
            $backupfile .= '.gz';
            $fp = @gzopen($backupfile, 'w');
        } else {
            $fp = @fopen($backupfile, 'w');
        }

        if ($fp === false) {
            echo Context::getContext()->getTranslator()->trans('Unable to create backup file', array(), 'Admin.Advparameters.Notification') . ' "' . addslashes($backupfile) . '"';

            return false;
        }

        $this->id = realpath($backupfile);

        fwrite($fp, '/* Backup for ' . Tools::getHttpHost(false, false) . __PS_BASE_URI__ . "\n *  at " . date($date) . "\n */\n");
        fwrite($fp, "\n" . 'SET NAMES \'utf8\';');
        fwrite($fp, "\n" . 'SET FOREIGN_KEY_CHECKS = 0;');
        fwrite($fp, "\n" . 'SET SESSION sql_mode = \'\';' . "\n\n");

        // Find all tables
        $tables = Db::getInstance()->executeS('SHOW TABLES');
        $found = 0;
        foreach ($tables as $table) {
            $table = current($table);

            // Skip tables which do not start with _DB_PREFIX_
            if (strlen($table) < strlen(_DB_PREFIX_) || strncmp($table, _DB_PREFIX_, strlen(_DB_PREFIX_)) != 0) {
                continue;
            }

            // Export the table schema
            $schema = Db::getInstance()->executeS('SHOW CREATE TABLE `' . $table . '`');

            if (count($schema) != 1 || !isset($schema[0]['Table']) || !isset($schema[0]['Create Table'])) {
                fclose($fp);
                $this->delete();
                echo Context::getContext()->getTranslator()->trans('An error occurred while backing up. Unable to obtain the schema of %s', array($table), 'Admin.Advparameters.Notification');

                return false;
            }

            fwrite($fp, '/* Scheme for table ' . $schema[0]['Table'] . " */\n");

            if ($this->psBackupDropTable) {
                fwrite($fp, 'DROP TABLE IF EXISTS `' . $schema[0]['Table'] . '`;' . "\n");
            }

            fwrite($fp, $schema[0]['Create Table'] . ";\n\n");

            if (!in_array($schema[0]['Table'], $ignoreInsertTable)) {
                $data = Db::getInstance()->query('SELECT * FROM `' . $schema[0]['Table'] . '`', false);
                $sizeof = Db::getInstance()->numRows();
                $lines = explode("\n", $schema[0]['Create Table']);

                if ($data && $sizeof > 0) {
                    // Export the table data
                    fwrite($fp, 'INSERT INTO `' . $schema[0]['Table'] . "` VALUES\n");
                    $i = 1;
                    while ($row = Db::getInstance()->nextRow($data)) {
                        $s = '(';

                        foreach ($row as $field => $value) {
                            $tmp = "'" . pSQL($value, true) . "',";
                            if ($tmp != "'',") {
                                $s .= $tmp;
                            } else {
                                foreach ($lines as $line) {
                                    if (strpos($line, '`' . $field . '`') !== false) {
                                        if (preg_match('/(.*NOT NULL.*)/Ui', $line)) {
                                            $s .= "'',";
                                        } else {
                                            $s .= 'NULL,';
                                        }
                                        break;
                                    }
                                }
                            }
                        }
                        $s = rtrim($s, ',');

                        if ($i % 200 == 0 && $i < $sizeof) {
                            $s .= ");\nINSERT INTO `" . $schema[0]['Table'] . "` VALUES\n";
                        } elseif ($i < $sizeof) {
                            $s .= "),\n";
                        } else {
                            $s .= ");\n";
                        }

                        fwrite($fp, $s);
                        ++$i;
                    }
                }
            }
            ++$found;
        }

        fclose($fp);
        if ($found == 0) {
            $this->delete();
            echo Context::getContext()->getTranslator()->trans('No valid tables were found to backup.', array(), 'Admin.Advparameters.Notification');

            return false;
        }

        return true;
    }
}
