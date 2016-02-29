<?php
/*
* 2007-2015 PrestaShop
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
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2015 PrestaShop SA
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

namespace PrestaShop\PrestaShop\Adapter;

class ObjectSerializer
{
    public function toArray($object)
    {
        $arr = array();

        if (is_a($object, 'ObjectModel')) {
            $fields = $object::$definition['fields'];
            foreach ($fields as $field_name => $null) {
                $arr[$field_name] = $object->{$field_name};
            }
            $must_have = ['id'];
            foreach ($must_have as $field_name) {
                $arr[$field_name] = $object->{$field_name};
            }
        } else {
            $arr = (array)$object;
        }

        $must_remove = ['deleted', 'active'];
        foreach ($must_remove as $field_name) {
            if (isset($arr[$field_name])) {
                unset($arr[$field_name]);
            }
        }

        return $arr;
    }
}
