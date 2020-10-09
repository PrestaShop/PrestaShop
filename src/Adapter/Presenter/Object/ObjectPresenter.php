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

namespace PrestaShop\PrestaShop\Adapter\Presenter\Object;

use Exception;
use Hook;
use ObjectModel;
use PrestaShop\PrestaShop\Adapter\Presenter\PresenterInterface;
use Product;

class ObjectPresenter implements PresenterInterface
{
    /**
     * @param ObjectModel $object
     *
     * @return array
     *
     * @throws Exception
     */
    public function present($object)
    {
        if (!($object instanceof ObjectModel)) {
            throw new Exception('ObjectPresenter can only present ObjectModel classes');
        }

        $presentedObject = [];

        $fields = $object::$definition['fields'];
        foreach ($fields as $fieldName => $null) {
            $presentedObject[$fieldName] = $object->{$fieldName};
        }

        if ($object instanceof Product) {
            $presentedObject['ecotax_tax_inc'] = $object->getEcotax(null, true, true);
        }

        $presentedObject['id'] = $object->id;

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
     * Execute filterHtml hook for html Content for objectPresenter.
     *
     * @param string $type
     * @param ObjectModel $presentedObject
     * @param array $htmlFields
     */
    private function filterHtmlContent($type, &$presentedObject, $htmlFields)
    {
        if (!empty($htmlFields) && is_array($htmlFields)) {
            $filteredHtml = Hook::exec(
                'filterHtmlContent',
                [
                    'type' => $type,
                    'htmlFields' => $htmlFields,
                    'object' => $presentedObject,
                ],
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
