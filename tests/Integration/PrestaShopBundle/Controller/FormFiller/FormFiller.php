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

namespace Tests\Integration\PrestaShopBundle\Controller\FormFiller;

use FormField;
use Symfony\Component\DomCrawler\Field\ChoiceFormField;
use Symfony\Component\DomCrawler\Form;

class FormFiller
{
    /**
     * @param Form $form
     * @param array $formModifications
     *
     * @return Form
     */
    public function fillForm(Form $form, array $formModifications): Form
    {
        foreach ($formModifications as $fieldName => $formValue) {
            if (!is_array($formValue)) {
                /** @var FormField $formField */
                $formField = $form->get($fieldName);
                $formField->setValue($formValue);

                continue;
            }

            // For multi select checkboxes or select inputs
            /** @var ChoiceFormField[]|ChoiceFormField $formFields */
            $formFields = $form->get($fieldName);
            if (!is_array($formFields)) {
                $formFields->select($formValue);

                continue;
            }

            // Multiple checkboxes are returned as array
            foreach ($formFields as $formField) {
                if ('checkbox' !== $formField->getType()) {
                    $formField->select($formValue);

                    continue;
                }

                $optionValue = $formField->availableOptionValues()[0];
                if (in_array($optionValue, $formValue)) {
                    $formField->tick();
                } else {
                    $formField->untick();
                }
            }
        }

        return $form;
    }
}
