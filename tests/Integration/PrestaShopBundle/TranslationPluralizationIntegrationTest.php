<?php

namespace Tests\Integration\PrestaShopBundle;

use PrestaShopBundle\Translation\PrestaShopTranslatorTrait;
use PrestaShopBundle\Translation\TranslatorComponent;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Tools;

class TranslationPluralizationIntegrationTest extends WebTestCase
{
    use PrestaShopTranslatorTrait;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::declareRequiredConstants();
    }

    private static function declareRequiredConstants(): void
    {
        $configuration = require_once _PS_CACHE_DIR_ . 'appParameters.php';

        if (defined('_PS_BO_ALL_THEMES_DIR_')) {
            return;
        }

        define('_PS_BO_ALL_THEMES_DIR_', '');
        if (!defined('_PS_TAB_MODULE_LIST_URL_')) {
            define('_PS_TAB_MODULE_LIST_URL_', '');
        }
        if (!defined('_DB_SERVER_')) {
            define('_DB_SERVER_', 'localhost');
        }
        if (!defined('_DB_USER_')) {
            define('_DB_USER_', $configuration['parameters']['database_user']);
        }
        if (!defined('_DB_PASSWD_')) {
            define('_DB_PASSWD_', $configuration['parameters']['database_password']);
        }
        if (!defined('_DB_NAME_')) {
            define('_DB_NAME_', 'test_' . $configuration['parameters']['database_name']);
        }
        if (!defined('_DB_PREFIX_')) {
            define('_DB_PREFIX_', $configuration['parameters']['database_prefix']);
        }
        if (!defined('_COOKIE_KEY_')) {
            define('_COOKIE_KEY_', Tools::passwdGen(64));
        }
        if (!defined('_PS_VERSION_')) {
            define('_PS_VERSION_', '1.7');
        }
        if (!defined('_PS_ADMIN_DIR_')) {
            define('_PS_ADMIN_DIR_', '');
        }
    }

    public function testTransWithoutParameters()
    {
        parent::setUp();

        self::bootKernel();
        global $kernel;
        $kernel = self::$kernel;
        $translator = self::$kernel->getContainer()->get('translator');

        //$mock = $this->createMock(TranslatorComponent::class);

        self::assertEquals('Succesful deletion', $translator->trans('Succesful deletion', ['legacy' => 'htmlspecialchars'], 'Admin.Notifications.Success', null));
    }
}
