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

namespace PrestaShopBundle\Form;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;

class FormCloner
{
    public function cloneForm(FormInterface $form, array $options = [], array $cloneOptions = []): FormInterface
    {
        $formBuilder = $this->cloneFormBuilder($form, $options, $cloneOptions);

        return $formBuilder->getForm();
    }

    private function cloneFormBuilder(FormInterface $form, array $options = [], array $cloneOptions = []): FormBuilderInterface
    {
        $formBuilder = $this->createFormBuilder($form, $options);
        if (!isset($cloneOptions['clone_model_transformers']) || true === $cloneOptions['clone_model_transformers']) {
            foreach ($form->getConfig()->getModelTransformers() as $modelTransformer) {
                $formBuilder->addModelTransformer($modelTransformer);
            }
        }

        if (!isset($cloneOptions['clone_view_transformers']) || true === $cloneOptions['clone_view_transformers']) {
            foreach ($form->getConfig()->getViewTransformers() as $formViewTransformer) {
                $formBuilder->addViewTransformer($formViewTransformer);
            }
        }

        return $formBuilder;
    }

    private function createFormBuilder(FormInterface $form, array $options = []): FormBuilderInterface
    {
        $factory = $form->getConfig()->getFormFactory();
        $resolvedFormType = $form->getConfig()->getType();

        $formOptions = $form->getConfig()->getOptions();
        $formOptions = array_merge($formOptions, $options);
        if (null !== $form->getParent()) {
            // Never initialize child forms automatically
            $formOptions['auto_initialize'] = false;
        }

        $formBuilder = $resolvedFormType->createBuilder($factory, $form->getName(), $formOptions);
        $resolvedFormType->buildForm($formBuilder, $formBuilder->getOptions());

        return $formBuilder;
    }
}
