<?php
@ini_set('display_errors', 'on');
require '../../config/config.inc.php';

// useful variables

$language   = Context::getContext()->language;
$shop       = Context::getContext()->shop;
$dbPrefix   = _DB_PREFIX_;

// Enable URL rewriting

function enableURLRewriting()
{
    Configuration::updateValue('PS_REWRITING_SETTINGS', 1);
    Tools::generateHtaccess();
}

if (!Configuration::get('PS_REWRITING_SETTINGS')) {
    enableURLRewriting();
}

echo "- URL rewriting enabled\n";

//Enable returns

function enableReturns()
{
    Configuration::updateValue('PS_ORDER_RETURN', 1);
}

if (!Configuration::get('PS_ORDER_RETURN')) {
    enableReturns();
}

echo "- Returns enabled\n";

//Enable returns

function enableVouchers()
{
    Configuration::updateValue('PS_CART_RULE_FEATURE_ACTIVE', 1);
}

if (!CartRule::isFeatureActive()) {
    enableVouchers();
}

echo "- Vouchers enabled\n";

// Setup modules

function disableModule($moduleName)
{
    $module = Module::getInstanceByName($moduleName);
    $module->disable();
    echo "- module `$moduleName` disabled\n";
}

function hookModule($moduleName, $hookName)
{
    $dbPrefix   = _DB_PREFIX_;
    $module     = Module::getInstanceByName($moduleName);
    $moduleId   = $module->id;
    Db::getInstance()->execute(
        "DELETE FROM {$dbPrefix}hook_module WHERE id_module=$moduleId"
    );
    $module->registerHook($hookName);
    echo "- module `$moduleName` hooked to `$hookName`\n";
}

// disableModule('blocklayered');
disableModule('cheque'); // not yet working
hookModule('blocktopmenu', 'displayTop');
hookModule('blocklanguages', 'displayNav');
hookModule('blockcurrencies', 'displayNav');
hookModule('blockuserinfo', 'displayNav');
hookModule('blocksearch', 'displayHeaderMiddle');

// We need a customizable product: we add a single required text field to the product with id 1.

$customizableProduct = new Product(1, false, $language->id);

// Hijack the "_deleteOldLabels" method to remove existing labels
// (shouldn't be any but I want this script to be idempotent)
$refl = new ReflectionClass('Product');
$meth = $refl->getMethod('_deleteOldLabels');
$meth->setAccessible(true);
$meth->invoke($customizableProduct);

// First, create the label
$customizableProduct->createLabels(($fileFields = 0), ($textFields = 1));
$fields = $customizableProduct->getCustomizationFields();
$id_customization_field = current(current(current($fields)))['id_customization_field'];
// And inform the product that it has become customizable
$customizableProduct->customizable = 1;
$customizableProduct->text_fields = 1;
$customizableProduct->save();

// Then define it. There is unfortunately no API, so we encode the data in $_POST...
$_POST[implode('_', ['label', 1, $id_customization_field, $language->id, $shop->id])] = 'my field';
$_POST[implode('_', ['require', 1, $id_customization_field])] = true;
$customizableProduct->updateLabels();

echo "- added a required customizable text field to product #1\n";

// We need 2 languages for some tests
Language::checkAndAddLanguage('fr');
echo "- added French language just so that we have 2\n";

$order = new Order(5);
$history = new OrderHistory();
$history->id_order = $order->id;
$history->id_employee = 1;

$use_existings_payment = false;
if (!$order->hasInvoice()) {
    $use_existings_payment = true;
}
$history->changeIdOrderState(5, $order, $use_existings_payment);
$history->add();
echo "- Order number 5 is now delivered\n";

echo "Shop fixtures prepared for tests!\n";
