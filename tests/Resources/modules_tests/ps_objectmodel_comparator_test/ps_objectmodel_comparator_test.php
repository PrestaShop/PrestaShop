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

use PrestaShop\PrestaShop\Adapter\ObjectModelComparator;

if (!defined('_PS_VERSION_')) {
    exit;
}

class Ps_objectmodel_comparator_test extends Module
{
    public const PS_OBJECTMODEL_COMPARATOR_STATUS = 'PS_OBJECTMODEL_COMPARATOR_STATUS';
    public const PS_OBJECTMODEL_COMPARATOR_LAST_CALL_STATUS = 'PS_OBJECTMODEL_COMPARATOR_LAST_CALL_STATUS';

    public function __construct()
    {
        $this->name = 'ps_objectmodel_comparator_test';
        $this->need_instance = 1;
        $this->displayName = 'Ps object model comparator test';
        $this->description = 'Ps object model comparator test';
        $this->confirmUninstall = 'Are you sure that you want to delete all of your contacts?';
        $this->ps_versions_compliancy = ['min' => '9.0', 'max' => _PS_VERSION_];

        $this->version = '1.0.0';
        $this->author = 'PrestaShop';

        parent::__construct();
    }

    public function install(): bool
    {
        return parent::install() && $this->registerHook('actionObjectProductUpdateBefore');
    }

    public function hookActionObjectProductUpdateBefore(array $params): void
    {
        $object = $params['object'] ?? null;
        $comparator = $params['objectComparator'] ?? null;

        if (!is_a($object, Product::class) || !is_a($comparator, ObjectModelComparator::class)) {
            return;
        }

        Configuration::updateValue(
            self::PS_OBJECTMODEL_COMPARATOR_LAST_CALL_STATUS,
            $this->generateHookCallStatusKey($object->getObjectName(), $object->id)
        );

        $diff = $comparator->getDiff();
        $oldProductId = (int) $comparator->getOldObject()->id;

        if (!empty($diff) && isset($diff['price'])) {
            $oldValue = $diff['price']['old'] ?? null;
            $newValue = $diff['price']['new'] ?? null;

            $statusKey = $this->generateChangesStatusKey('Product', $oldProductId, 'price', (int) $oldValue, (int) $newValue);
        } else {
            $statusKey = $this->generateChangesStatusKey('Product', $oldProductId, 'price', (int) $comparator->getOldObject()->price, (int) $comparator->getNewObject()->price);
        }

        Configuration::updateValue(self::PS_OBJECTMODEL_COMPARATOR_STATUS, $statusKey);
    }

    public function getHookLastCallStatus(): string
    {
        return (string) Configuration::get(self::PS_OBJECTMODEL_COMPARATOR_LAST_CALL_STATUS);
    }

    public function getLastUpdatedProductStatus(): string
    {
        return (string) Configuration::get(self::PS_OBJECTMODEL_COMPARATOR_STATUS);
    }

    /**
     * Generates a unique status key representing a change in a specific field of an object.
     *
     * This key includes the object name, object ID, field name, old value, and new value
     * to provide a readable and traceable identifier for the change.
     *
     * @param string $objectName The name of the object (e.g., product, order).
     * @param int $idObject the unique identifier of the object
     * @param string $field the name of the field that was changed
     * @param int $oldValue the old value of the field before the change
     * @param int $newValue the new value of the field after the change
     *
     * @return string a formatted string representing the change status key
     */
    protected function generateChangesStatusKey(string $objectName, int $idObject, string $field, int $oldValue, int $newValue): string
    {
        $stringFormat = '%s_%d_%s_has_been_changed_old_value_%s_new_value_%s';

        if ($oldValue == $newValue) {
            $stringFormat = '%s_%d_%s_no_change_detected_old_value_%s_new_value_%s';
        }

        return sprintf(
            $stringFormat,
            $objectName,
            $idObject,
            $field,
            $oldValue,
            $newValue
        );
    }

    protected function generateHookCallStatusKey(string $objectName, int $idObject): string
    {
        return sprintf(
            'Called_during_update_%s_object_model_%d',
            $objectName,
            $idObject
        );
    }
}
