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

namespace PrestaShopBundle\Form\Admin\Improve\International\Translations;

use PrestaShop\PrestaShop\Core\Form\FormChoiceProviderInterface;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class ModifyTranslationsType is responsible for building 'Modify translations' form
 * in 'Improve > International > Translations' page
 */
class ModifyTranslationsType extends TranslatorAwareType
{
    /**
     * @var FormChoiceProviderInterface
     */
    private $translationTypeChoiceProvider;

    /**
     * @var FormChoiceProviderInterface
     */
    private $emailContentTypeChoiceProvider;

    /**
     * @var FormChoiceProviderInterface
     */
    private $themeChoiceProvider;

    /**
     * @var FormChoiceProviderInterface
     */
    private $moduleChoiceProvider;

    /**
     * @param TranslatorInterface $translator
     * @param array $locales
     * @param FormChoiceProviderInterface $translationTypeChoiceProvider
     * @param FormChoiceProviderInterface $emailContentTypeChoiceProvider
     * @param FormChoiceProviderInterface $themeChoiceProvider
     * @param FormChoiceProviderInterface $moduleChoiceProvider
     */
    public function __construct(
        TranslatorInterface $translator,
        array $locales,
        FormChoiceProviderInterface $translationTypeChoiceProvider,
        FormChoiceProviderInterface $emailContentTypeChoiceProvider,
        FormChoiceProviderInterface $themeChoiceProvider,
        FormChoiceProviderInterface $moduleChoiceProvider
    ) {
        parent::__construct($translator, $locales);
        $this->translationTypeChoiceProvider = $translationTypeChoiceProvider;
        $this->emailContentTypeChoiceProvider = $emailContentTypeChoiceProvider;
        $this->themeChoiceProvider = $themeChoiceProvider;
        $this->moduleChoiceProvider = $moduleChoiceProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $noTheme = $this->trans('Core (no theme selected)', 'Admin.International.Feature');

        $builder
            ->add('translation_type', ChoiceType::class, [
                'choices' => $this->translationTypeChoiceProvider->getChoices(),
            ])
            ->add('email_content_type', ChoiceType::class, [
                'choices' => $this->emailContentTypeChoiceProvider->getChoices(),
            ])
            ->add('theme', ChoiceType::class, [
                'choices' =>
                    [$noTheme => ''] +
                    $this->themeChoiceProvider->getChoices(),
                'choice_attr' => [
                    $noTheme => [
                        'class' => 'js-no-theme'
                    ],
                ],
            ])
            ->add('module', ChoiceType::class, [
                'placeholder' => '---',
                'choices' => $this->moduleChoiceProvider->getChoices(),
            ])
            ->add('language', ChoiceType::class, [
                'placeholder' => $this->trans('Language', 'Admin.Global'),
                'choices' => $this->getLocaleChoices(),
            ])
        ;
    }
}
