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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace Tests\Integration\PrestaShopBundle\Controller\FormFiller;

use DOMElement;
use InvalidArgumentException;
use PrestaShopBundle\Form\Admin\Extension\DisablingSwitchExtension;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\DomCrawler\Field\ChoiceFormField;
use Symfony\Component\DomCrawler\Field\FormField;
use Symfony\Component\DomCrawler\Field\InputFormField;
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
                $this->enabledAssociatedField($form, $formField);

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

    /**
     * Some fields are based on the DisablingSwitchExtension in regular FO the field are enabled by JS but here
     * we need to force this or the data will be removed from the $form->getData()
     *
     * @param Form $form
     * @param FormField $formField
     */
    private function enabledAssociatedField(Form $form, FormField $formField): void
    {
        if (strpos($formField->getName(), DisablingSwitchExtension::FIELD_PREFIX) === false) {
            return;
        }

        $associatedFieldName = str_replace(DisablingSwitchExtension::FIELD_PREFIX, '', $formField->getName());
        try {
            /** @var InputFormField $associatedField */
            $associatedField = $form->get($associatedFieldName);
        } catch (InvalidArgumentException $e) {
            return;
        }

        $formCrawler = new Crawler($form->getFormNode());
        $fieldCrawler = $formCrawler->filter(sprintf('[name="%s"]', $associatedFieldName));
        if (!$fieldCrawler->count()) {
            return;
        }

        $fieldNode = $fieldCrawler->getNode(0);
        if (!$fieldNode instanceof DOMElement) {
            return;
        }

        $isDisabled = $associatedField->isDisabled();
        $isDisabling = (bool) $formField->getValue() === false;
        if ($isDisabled && !$isDisabling) {
            $fieldNode->removeAttribute('disabled');
        } elseif (!$isDisabled && $isDisabling) {
            $fieldNode->setAttribute('disabled', 'disabled');
        }
    }
}
