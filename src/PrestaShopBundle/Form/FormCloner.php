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

namespace PrestaShopBundle\Form;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;

/**
 * This class is used to clone a form field, it is interesting when you need to change an option
 * of a form already created. Since the options are immutable once the form is built the solution
 * is to rebuild it while overriding its option and then add it again to the parent form to replace
 * the previous one.
 */
class FormCloner
{
    /**
     * @param FormInterface $form
     * @param array $options
     *
     * @return FormInterface
     */
    public function cloneForm(FormInterface $form, array $options = []): FormInterface
    {
        $formBuilder = $this->cloneFormBuilder($form, $options);

        return $formBuilder->getForm();
    }

    /**
     * @param FormInterface $form
     * @param array $options
     *
     * @return FormBuilderInterface
     */
    private function cloneFormBuilder(FormInterface $form, array $options = []): FormBuilderInterface
    {
        $formBuilder = $this->createFormBuilder($form, $options);
        foreach ($form->getConfig()->getModelTransformers() as $initialModelTransformer) {
            if (!$this->hasSimilarTransformer($initialModelTransformer, $formBuilder->getModelTransformers())) {
                $formBuilder->addModelTransformer($initialModelTransformer);
            }
        }

        foreach ($form->getConfig()->getViewTransformers() as $initialViewTransformer) {
            if (!$this->hasSimilarTransformer($initialViewTransformer, $formBuilder->getViewTransformers())) {
                $formBuilder->addViewTransformer($initialViewTransformer);
            }
        }

        return $formBuilder;
    }

    /**
     * Most of the time the transformers are built by the type itself and will be cloned thanks to the builder,
     * but if the transformer was added manually or outside of the FormType (by the parent maybe) it is not handled
     * by the builder. So we test if the initial transformer is amongst the cloned ones (by checking their type) to
     * check if they need to be copied.
     *
     * Maybe this test of class is too simple and will have to be improved someday.
     *
     * @param DataTransformerInterface $initialTransformer
     * @param array $clonedTransformers
     *
     * @return bool
     */
    private function hasSimilarTransformer(DataTransformerInterface $initialTransformer, array $clonedTransformers): bool
    {
        foreach ($clonedTransformers as $clonedTransformer) {
            if ($initialTransformer instanceof $clonedTransformer) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param FormInterface $form
     * @param array $options
     *
     * @return FormBuilderInterface
     */
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
