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
declare(strict_types=1);

namespace PrestaShopBundle\Bridge\Helper\Form;

use HelperForm;
use PrestaShopBundle\Bridge\AdminController\Field\FormField;

class HelperFormBridge
{
    /**
     * @param HelperFormConfiguration $helperFormConfiguration
     *
     * @return string|null
     */
    public function generate(HelperFormConfiguration $helperFormConfiguration): ?string
    {
        $helperForm = new HelperForm();
        //@todo: transform FormField classes to legacy supported array
        $formFields = $helperFormConfiguration->getFormFields();

        $legacyFormFields = [];
        foreach ($formFields as $formField) {
            $type = $formField->getType();
            $config = $formField->getConfig();

            // @todo: need to investigate if any other fields acted like this
            // legacy type "input" could contain multiple fields configs inside it like this:
            //            'input' => [
            //                [
            //                    'type' => 'text',
            //                    'label' => $this->trans('Alias', [], 'Admin.Shopparameters.Feature'),
            //                    'name' => 'alias',
            //                ],
            //                [
            //                    'type' => 'text',
            //                    'label' => $this->trans('Result', [], 'Admin.Shopparameters.Feature'),
            //                    'name' => 'search',
            //                ],
            //            ],
            if (FormField::TYPE_INPUT === $type) {
                $legacyFormFields[$type][] = $config;

                //@todo: without this smarty fails. Each field name  must exist in fields_value list as index key
                $helperForm->fields_value[$config['name']] = $formField->getValue();

                continue;
            }

            $legacyFormFields[$type] = $config;
        }

        /* @see \AdminController::renderForm() */
        return $helperForm->generateForm([['form' => $legacyFormFields]]);
    }
}
