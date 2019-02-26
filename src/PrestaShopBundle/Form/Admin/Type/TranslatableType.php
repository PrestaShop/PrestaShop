<?php
/**
 * 2007-2019 PrestaShop and Contributors
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShopBundle\Form\Admin\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormErrorIterator;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class TranslatableType adds translatable inputs with custom inner type to forms.
 */
class TranslatableType extends AbstractType
{
    /**
     * @var array
     */
    private $locales;

    /**
     * @param array $locales
     */
    public function __construct(array $locales)
    {
        $this->locales = $locales;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        foreach ($options['locales'] as $locale) {
            $typeOptions = $options['options'];
            $typeOptions['label'] = $locale['iso_code'];

            if (!isset($typeOptions['required'])) {
                $typeOptions['required'] = false;
            }

            $typeOptions['attr']['data-lang-id'] = $locale['id_lang'];

            $builder->add($locale['id_lang'], $options['type'], $typeOptions);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['locales'] = $options['locales'];
        $view->vars['default_locale'] = reset($options['locales']);
        $view->vars['hide_locales'] = 1 >= count($options['locales']);

        $this->setErrorsByLocale($view, $form, $options['locales']);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'type' => TextType::class,
            'options' => [],
            'locales' => $this->locales,
        ]);

        $resolver->setAllowedTypes('locales', 'array');
        $resolver->setAllowedTypes('options', 'array');
        $resolver->setAllowedTypes('type', 'string');
        $resolver->setAllowedTypes('error_bubbling', 'bool');
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'translatable';
    }

    /**
     * If there are more then one locale it gets nested errors and if found prepares the errors for usage in twig.
     * If there are only one error which is not assigned to the default language then the error is being localised.
     *
     * @param FormView $view
     * @param FormInterface $form
     * @param array $locales
     */
    private function setErrorsByLocale(FormView $view, FormInterface $form, array $locales)
    {
        if (count($locales) <= 1) {
            return;
        }

        $formErrors = $form->getErrors(true);

        if (empty($formErrors)) {
            return;
        }

        if (1 === count($formErrors)) {
            $errorByLocale = $this->getSingleTranslatableErrorExcludingDefaultLocale(
                $formErrors,
                $form,
                $locales
            );

            if (null !== $errorByLocale) {
                $view->vars['error_by_locale'] = $errorByLocale;
            }

            return;
        }

        $errorsByLocale = $this->getTranslatableErrors(
            $formErrors,
            $form,
            $locales
        );

        if (null !== $errorsByLocale) {
            $view->vars['errors_by_locale'] = $errorsByLocale;
        }
    }

    /**
     * Gets single error excluding the default locales error since for default locale a language name prefix is not
     * required.
     *
     * @param FormErrorIterator $formErrors
     * @param FormInterface $form
     * @param array $locales
     *
     * @return array|null
     */
    private function getSingleTranslatableErrorExcludingDefaultLocale(
        FormErrorIterator $formErrors,
        FormInterface $form,
        array $locales
    ) {
        $errorByLocale = null;
        $formError = $formErrors[0];
        $nonDefaultLanguageFormKey = null;
        $iteration = 0;

        foreach ($form as $formItem) {
            if (0 === $iteration) {
                ++$iteration;

                continue;
            }

            if ($this->doesErrorFormAndCurrentFormMatches($formError->getOrigin(), $formItem)) {
                $nonDefaultLanguageFormKey = $iteration;

                break;
            }

            ++$iteration;
        }

        if (isset($locales[$nonDefaultLanguageFormKey])) {
            $errorByLocale = [
                'locale_name' => $locales[$nonDefaultLanguageFormKey]['name'],
                'error_message' => $formError->getMessage(),
            ];
        }

        return $errorByLocale;
    }

    /**
     * Gets translatable errors ready for popover display and assigned to each language
     *
     * @param FormErrorIterator $formErrors
     * @param FormInterface $form
     * @param array $locales
     *
     * @return array|null
     */
    private function getTranslatableErrors(
        FormErrorIterator $formErrors,
        FormInterface $form,
        array $locales
    ) {
        $errorsByLocale = null;
        $iteration = 0;
        foreach ($form as $formItem) {
            $doesLocaleExistForInvalidForm = isset($locales[$iteration]) && !$formItem->isValid();

            if ($doesLocaleExistForInvalidForm) {
                foreach ($formErrors as $formError) {
                    if ($this->doesErrorFormAndCurrentFormMatches($formError->getOrigin(), $formItem)) {
                        $errorsByLocale[$locales[$iteration]['iso_code']] = [
                            'locale_name' => $locales[$iteration]['name'],
                            'error_message' => $formError->getMessage(),
                        ];
                    }
                }
            }

            ++$iteration;
        }

        return $errorsByLocale;
    }

    /**
     * Determines if the error form matches the given form. Used for mapping the locales for the form fields.
     *
     * @param FormInterface $errorForm
     * @param FormInterface $currentForm
     *
     * @return bool
     */
    private function doesErrorFormAndCurrentFormMatches(FormInterface $errorForm, FormInterface $currentForm)
    {
        return $errorForm === $currentForm;
    }
}
