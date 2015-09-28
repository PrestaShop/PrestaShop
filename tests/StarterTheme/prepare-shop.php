<?php
@ini_set('display_errors', 'on');
require '../../config/config.inc.php';

// useful variables

$language = Context::getContext()->language;
$shop     = Context::getContext()->shop;

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

echo "Shop fixtures prepared for tests!\n";
