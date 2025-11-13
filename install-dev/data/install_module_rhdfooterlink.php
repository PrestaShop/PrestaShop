<?php
require_once __DIR__ . '/../../config/config.inc.php';

// Boot Symfony kernel so services like Translator are available during CLI install
global $kernel;
if (!isset($kernel) || !$kernel) {
    require_once __DIR__ . '/../../app/FrontKernel.php';
    $env = _PS_MODE_DEV_ ? 'dev' : 'prod';
    $debug = (bool) _PS_MODE_DEV_;
    $kernel = new \FrontKernel($env, $debug);
    $kernel->boot();
}

$moduleName = 'rhdfooterlink';
$module = Module::getInstanceByName($moduleName);
if (!$module) {
    echo "Module $moduleName not found.\n";
    exit(1);
}
if (!$module->isInstalled($moduleName)) {
    if ($module->install()) {
        echo "✓ Installed module $moduleName\n";
    } else {
        echo "✗ Failed to install module $moduleName\n";
        exit(1);
    }
} else {
    echo "• Module $moduleName already installed\n";
}
// Ensure module is enabled and hook registered
if (!$module->isEnabledForShopContext()) {
    $module->enable(true);
    echo "✓ Enabled module $moduleName\n";
}
if ($module->registerHook('displayFooter')) {
    echo "✓ Hook displayFooter registered\n";
}
// Ensure CMS ID configuration
$idLang = (int) Configuration::get('PS_LANG_DEFAULT');
$idCms = (int) Db::getInstance()->getValue(
        'SELECT cl.id_cms FROM ' . _DB_PREFIX_ . 'cms_lang cl
         WHERE cl.id_lang=' . $idLang . ' AND (
             cl.link_rewrite = "informacja-o-rhd" OR cl.meta_title = "Informacja o RHD (Rolniczy Handel Detaliczny)"
         )'
);
Configuration::updateValue('RHD_FOOTER_CMS_ID', $idCms ?: 0);

echo "RHD_FOOTER_CMS_ID=" . (int) Configuration::get('RHD_FOOTER_CMS_ID') . "\n";
// Clear caches so hook/template is picked up
Tools::clearAllCache();
