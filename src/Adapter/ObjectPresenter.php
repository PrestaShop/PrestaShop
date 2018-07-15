<?php
/**
 * 2007-2018 PrestaShop
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

namespace PrestaShop\PrestaShop\Adapter;

use PrestaShop\PrestaShop\Core\Foundation\Templating\PresenterInterface;
use Hook;

/**
 * A simple and reusable Presenter for an instance of ObjectModel.
 */
class ObjectPresenter implements PresenterInterface
{
    public function present($object)
    {
        if (!is_a($object, 'ObjectModel')) {
            throw new \Exception('ObjectPresenter can only present ObjectModel classes');
        }

        $presentedObject = array();

        $fields = $object::$definition['fields'];
        foreach ($fields as $fieldName => $null) {
            $presentedObject[$fieldName] = $object->{$fieldName};
        }
        $mustHave = ['id'];
        foreach ($mustHave as $fieldName) {
            $presentedObject[$fieldName] = $object->{$fieldName};
        }

        $mustRemove = ['deleted', 'active'];
        foreach ($mustRemove as $fieldName) {
            if (isset($presentedObject[$fieldName])) {
                unset($presentedObject[$fieldName]);
            }
        }

        $this->filterHtmlContent($object::$definition['table'], $presentedObject, $object->getHtmlFields());

        return $presentedObject;
    }

    /**
     * Execute filterHtml hook for html Content for objectPresenter
     *
     * @param $type
     * @param $presentedObject
     * @param $htmlFields
     */
    private function filterHtmlContent($type, &$presentedObject, $htmlFields)
    {
        if (!empty($htmlFields) && is_array($htmlFields)) {
            $filteredHtml = Hook::exec(
                'filterHtmlContent',
                array(
                    'type' => $type,
                    'htmlFields' => $htmlFields,
                    'object' => $presentedObject,
                ),
                null,
                false,
                true,
                false,
                null,
                true
            );

            if (!empty($filteredHtml['object'])) {
                $presentedObject = $filteredHtml['object'];
            }
        }
    }
}
