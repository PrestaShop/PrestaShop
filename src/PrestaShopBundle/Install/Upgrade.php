<?php
/**
 * 2007-2017 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
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
 * @copyright 2007-2017 PrestaShop SA
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace {
    $root_dir = realpath(__DIR__.'/../../..');

    require_once $root_dir.'/vendor/paragonie/random_compat/lib/random.php';

    if (!class_exists('PhpEncryptionEngine')) {
        require_once $root_dir.'/classes/PhpEncryptionEngine.php';
        class PhpEncryptionEngine extends \PhpEncryptionEngineCore {}
    }

    if (!class_exists('PhpEncryptionLegacyEngine')) {
        require_once $root_dir.'/classes/PhpEncryptionLegacyEngine.php';
        class PhpEncryptionLegacyEngine extends \PhpEncryptionLegacyEngineCore {}
    }

    if (!class_exists('PhpEncryption')) {
        require_once $root_dir.'/classes/PhpEncryption.php';
        class PhpEncryption extends \PhpEncryptionCore {}
    }
}

namespace PrestaShopBundle\Install {

    use Symfony\Component\Yaml\Yaml;
    use Symfony\Component\Filesystem\Filesystem;
    use Symfony\Component\Filesystem\Exception\IOException;
    use Context;
    use RandomLib;
    use Composer\Script\Event;
    use PhpEncryption;

    class Upgrade
    {
        /** @var \FileLogger */
        private $logger;
        private $infoList = array();
        private $warningList = array();
        private $failureList = array();
        private $nextQuickInfo = array();
        private $nextErrors = array();
        private $next;
        private $nextDesc;
        private $inAutoUpgrade = false;
        private $translator;
        // used for translations
        public static $l_cache;


        public function __construct($cacheDir)
        {
            $this->logger = new \FileLogger();
            $this->logger->setFilename($cacheDir.@date('Ymd').'_upgrade.log');
            $this->translator = Context::getContext()->getTranslator();
            $this->nextDesc = $this->getTranslator()->trans('Database upgrade completed.');
        }

        public function getTranslator() {
            return $this->translator;
        }

        public function logInfo($quickInfo, $id = null,
                                $transVariables = array(), $dbInfo = false) {
            $info = $this->getTranslator()->trans($quickInfo, $transVariables,
                'Install.Upgrade.Error');
            if ($this->inAutoUpgrade) {
                if ($dbInfo) {
                    $this->nextQuickInfo[] = '<div class="upgradeDbOk">' . $info . '</div>';
                } else {
                    $this->nextQuickInfo[] = $info;
                }
                $this->infoList[] = $info;
            } else {
                if (!empty($quickInfo)) {
                    $this->logger->logInfo($info);
                }
                if ($id !== null) {
                    if (!is_numeric($id)) {
                        $customInfo = '<action result="info" id="' . $id . '"><![CDATA[' . htmlentities($info) . "]]></action>\n";
                    } else {
                        $customInfo = '<action result="info" id="' . $id . '" />' . "\n";
                    }
                    $this->infoList[] = $customInfo;
                }
            }
        }

        public function logWarning($quickInfo, $id,
                                    $transVariables = array(), $dbInfo = false) {
            $info = $this->getTranslator()->trans($quickInfo, $transVariables,
                'Install.Upgrade.Error');
            if ($this->inAutoUpgrade) {
                if ($dbInfo) {
                    $this->nextQuickInfo[] = '<div class="upgradeDbError">' . $info . '</div>';
                } else {
                    $this->nextQuickInfo[] = $info;
                }
                $this->nextErrors[] = $info;
                $this->warningList[] = $info;
                if (empty($this->failureList)) {
                    $this->nextDesc = $this->getTranslator()->trans('Warning detected during upgrade.');
                }
            } else {
                if (!empty($quickInfo)) {
                    $this->logger->logWarning($info);
                }
                if ($id !== null) {
                    if (!is_numeric($id)) {
                        $customWarning = '<action result="warning" id="' . $id . '"><![CDATA[' . htmlentities($info) . "]]></action>\n";
                    } else {
                        $customWarning = '<action result="warning" id="' . $id . '" />' . "\n";
                    }
                    $this->warningList[] = $customWarning;
                }
            }
        }

        public function logError($quickInfo, $id,
                                 $transVariables = array(), $dbInfo = false) {
            $info = $this->getTranslator()->trans($quickInfo, $transVariables,
                'Install.Upgrade.Error');
            if ($this->inAutoUpgrade) {
                if ($dbInfo) {
                    $this->nextQuickInfo[] = '<div class="upgradeDbError">' . $info . '</div>';
                } else {
                    $this->nextQuickInfo[] = $info;
                }
                $this->nextErrors[] = $info;
                $this->failureList[] = $info;
                $this->nextDesc = $this->getTranslator()->trans('Error detected during upgrade.');
                $this->next = 'error';
            } else {
                if (!empty($quickInfo)) {
                    $this->logger->logError($info);
                }
                if ($id !== null) {
                    if (!is_numeric($id)) {
                        $customError = '<action result="error" id="' . $id . '"><![CDATA[' . htmlentities($info) . "]]></action>\n";
                    } else {
                        $customError = '<action result="error" id="' . $id . '" />' . "\n";
                    }
                    $this->failureList[] = $customError;
                }
            }
        }

        public function getInAutoUpgrade() {
            return $this->inAutoUpgrade;
        }

        public function setInAutoUpgrade($value) {
            $this->inAutoUpgrade = $value;
        }

        public function getNext() {
            return $this->next;
        }

        public function getNextDesc() {
            return $this->nextDesc;
        }

        public function getInfoList() {
            return $this->infoList;
        }

        public function getWarningList() {
            return $this->warningList;
        }

        public function getFailureList() {
            return $this->failureList;
        }

        public function getNextQuickInfo() {
            return $this->nextQuickInfo;
        }

        public function getNextErrors() {
            return $this->nextErrors;
        }

        public function hasInfo() {
            return !empty($this->infoList);
        }

        public function hasWarning() {
            return !empty($this->warningList);
        }

        public function hasFailure() {
            return !empty($this->failureList);
        }

        const SETTINGS_FILE = 'config/settings.inc.php';

        public static function migrateSettingsFile(Event $event = null)
        {
            if ($event !== null) {
                $event->getIO()->write('Migrating old setting file...');
            }

            $root_dir = realpath(__DIR__ . '/../../../');

            $phpParametersFilepath = $root_dir . '/app/config/parameters.php';
            if (file_exists($phpParametersFilepath)) {
                if ($event !== null) {
                    $event->getIO()->write('parameters file already exists!');
                    $event->getIO()->write('Finished...');
                }

                return false;
            }

            if (!file_exists($phpParametersFilepath) && !file_exists($root_dir.'/app/config/parameters.yml')
                && !file_exists($root_dir.'/'.self::SETTINGS_FILE)) {
                if ($event !== null) {
                    $event->getIO()->write('No file to migrate!');
                    $event->getIO()->write('Finished...');
                }
                return false;
            }

            $filesystem = new Filesystem();
            $exportPhpConfigFile = function ($config, $destination) use ($filesystem) {
                try {
                    $filesystem->dumpFile($destination, '<?php return ' . var_export($config, true) . ';' . "\n");
                } catch (IOException $e) {
                    return false;
                }

                return true;
            };

            $fileMigrated = false;
            $default_parameters = Yaml::parse(file_get_contents($root_dir . '/app/config/parameters.yml.dist'));
            $default_parameters['parameters']['new_cookie_key'] = PhpEncryption::createNewRandomKey();
            if (file_exists($root_dir . '/' . self::SETTINGS_FILE)) {
                $tmp_settings = file_get_contents($root_dir . '/' . self::SETTINGS_FILE);
            } else {
                $tmp_settings = null;
            }

            if (!file_exists($root_dir . '/app/config/parameters.yml') && $tmp_settings && strpos($tmp_settings, '_DB_SERVER_') !== false) {
                $tmp_settings = preg_replace('/(\'|")\_/', '$1_LEGACY_', $tmp_settings);
                $tmp_settings_file = str_replace('/settings', '/tmp_settings', $root_dir . '/' . self::SETTINGS_FILE);
                file_put_contents($tmp_settings_file, $tmp_settings);
                include $tmp_settings_file;
                @unlink($tmp_settings_file);
                $factory = new RandomLib\Factory();
                $generator = $factory->getLowStrengthGenerator();
                $secret = $generator->generateString(56);

                if (!defined('_LEGACY_NEW_COOKIE_KEY_')) {
                    define('_LEGACY_NEW_COOKIE_KEY_', $default_parameters['parameters']['new_cookie_key']);
                }

                $db_server_port = explode(':', _LEGACY_DB_SERVER_);
                if (count($db_server_port) == 1) {
                    $db_server = $db_server_port[0];
                    $db_port = 3306;
                } else {
                    $db_server = $db_server_port[0];
                    $db_port = $db_server_port[1];
                }

                $parameters = array(
                    'parameters' => array(
                            'database_host' => $db_server,
                            'database_port' => $db_port,
                            'database_user' => _LEGACY_DB_USER_,
                            'database_password' => _LEGACY_DB_PASSWD_,
                            'database_name' => _LEGACY_DB_NAME_,
                            'database_prefix' => _LEGACY_DB_PREFIX_,
                            'database_engine' => _LEGACY_MYSQL_ENGINE_,
                            'cookie_key' => _LEGACY_COOKIE_KEY_,
                            'cookie_iv' => _LEGACY_COOKIE_IV_,
                            'new_cookie_key' => _LEGACY_NEW_COOKIE_KEY_,
                            'ps_caching' => _LEGACY_PS_CACHING_SYSTEM_,
                            'ps_cache_enable' => _LEGACY_PS_CACHE_ENABLED_,
                            'ps_creation_date' => _LEGACY_PS_CREATION_DATE_,
                            'secret' => $secret,
                            'mailer_transport' => 'smtp',
                            'mailer_host' => '127.0.0.1',
                            'mailer_user' => '',
                            'mailer_password' => '',
                        ) + $default_parameters['parameters'],
                );
            } elseif (file_exists($root_dir . '/app/config/parameters.yml')) {
                $parameters = Yaml::parse(file_get_contents($root_dir . '/app/config/parameters.yml'));
                if (empty($parameters['parameters'])) {
                    $parameters['parameters'] = array();
                }
                // add potentially missing default entries
                $parameters['parameters'] = $parameters['parameters'] + $default_parameters['parameters'];
            } else {
                $parameters = $default_parameters;
            }

            if (!empty($parameters) && $exportPhpConfigFile($parameters, $phpParametersFilepath)) {
                $fileMigrated = true;
                $settings_content = "<?php\n";
                $settings_content .= '//@deprecated 1.7';

                file_put_contents($root_dir . '/' . self::SETTINGS_FILE, $settings_content);
                file_put_contents($root_dir . '/app/config/parameters.yml', 'parameters:');
            }

            if ($event !== null) {
                if (!$fileMigrated) {
                    $event->getIO()->write('No old config file present!');
                }
                $event->getIO()->write('Finished...');
            }
            return true;
        }
    }
}
