#!/usr/bin/env php
<?php

use PrestaShopBundle\Install\DatabaseDump;
use PrestaShopBundle\Install\Install;
use Tests\Resources\ResourceResetter;

define('_PS_ROOT_DIR_', dirname(__DIR__));
const _PS_IN_TEST_ = true;
const __PS_BASE_URI__ = '/';
const _PS_MODULE_DIR_ = _PS_ROOT_DIR_ . '/modules/';

require_once _PS_ROOT_DIR_ . '/install-dev/init.php';

$install = new Install();
$install->setTranslator(Context::getContext()->getTranslatorFromLocale('en'));
DbPDOCore::createDatabase(_DB_SERVER_, _DB_USER_, _DB_PASSWD_, _DB_NAME_);
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

// Default language is forced as en, we need french translation package as well, we only need the catalog to
// be available for the Translator component but we do not want the Language in the DB
if (!Language::translationPackIsInCache('fr-FR')) {
    Language::downloadXLFLanguagePack('fr-FR');
}
Language::installSfLanguagePack('fr-FR');

$install->installFixtures();

Category::regenerateEntireNtree();
Tab::resetStaticCache();
$install->installTheme();
$install->installModules();

DatabaseDump::create();

$resourceResetter = new ResourceResetter();
$resourceResetter->backupImages();
$resourceResetter->backupDownloads();
