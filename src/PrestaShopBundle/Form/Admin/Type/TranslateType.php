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

namespace PrestaShopBundle\Form\Admin\Type;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * This form class is responsible to create a translatable form.
 * Language selection uses tabs.
 * 
 * @link https://devdocs.prestashop-project.org/8/development/components/form/types-reference/translate-type/
 */
class TranslateType extends CommonAbstractType
{
    /**
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;

    /**
     * @var bool
     */
    private $saveFormLocaleChoice;

    /**
     * @var int
     */
    private $defaultFormLanguageId;

    /**
     * @var int
     */
    private $defaultShopLanguageId;

    /**
     * @param UrlGeneratorInterface $urlGenerator
     * @param bool $saveFormLocaleChoice
     * @param int $defaultFormLanguageId
     * @param int $defaultShopLanguageId
     */
    public function __construct(
        UrlGeneratorInterface $urlGenerator,
        $saveFormLocaleChoice,
        $defaultFormLanguageId,
        $defaultShopLanguageId
    ) {
        $this->urlGenerator = $urlGenerator;
        $this->saveFormLocaleChoice = $saveFormLocaleChoice;
        $this->defaultFormLanguageId = $defaultFormLanguageId;
        $this->defaultShopLanguageId = $defaultShopLanguageId;
    }

    /**
     * {@inheritdoc}
     *
     * Builds form fields for each locales
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $i = 0;
        foreach ($options['locales'] as $locale) {
            $options['options']['empty_data'] = '';
            $locale_options = $options['options'];
            $locale_options['label'] = $locale['iso_code'];
            if ($i > 0) {
                $locale_options['required'] = false;
                unset($locale_options['constraints']);
            }

            $builder->add($locale['id_lang'], $options['type'], $locale_options);
            ++$i;
        }
    }

    /**
     * {@inheritdoc}
     *
     * Add the var locales and defaultLocale to the view
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['locales'] = $options['locales'];
        $view->vars['defaultLocale'] = $this->getDefaultLocale($options['locales']);
        $view->vars['hideTabs'] = $options['hideTabs'];

        if ($this->saveFormLocaleChoice) {
            $view->vars['change_form_language_url'] = $this->urlGenerator->generate(
                'admin_employees_change_form_language'
            );
        }
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'type' => null,
            'options' => [],
            'locales' => [],
            'hideTabs' => true,
        ]);
    }

    /**
     * Returns the block prefix of this type.
     *
     * @return string The prefix name
     */
    public function getBlockPrefix()
    {
        return 'translatefields';
    }

    /**
     * Get default locale.
     *
     * @param array $locales
     *
     * @return array
     */
    private function getDefaultLocale(array $locales)
    {
        // If default form language is not available we will use default shop language
        $languageId = $this->defaultFormLanguageId ?: $this->defaultShopLanguageId;

        // Searching for a locale that matches the selected language
        foreach ($locales as $locale) {
            if ($locale['id_lang'] == $languageId) {
                return $locale;
            }
        }

        return reset($locales);
    }
}
