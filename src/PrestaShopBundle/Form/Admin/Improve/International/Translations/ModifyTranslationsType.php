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

namespace PrestaShopBundle\Form\Admin\Improve\International\Translations;

use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class ModifyTranslationsType is responsible for building 'Modify translations' form
 * in 'Improve > International > Translations' page.
 */
class ModifyTranslationsType extends TranslatorAwareType
{
    /**
     * @var array
     */
    private $translationTypeChoices;

    /**
     * @var array
     */
    private $emailContentTypeChoices;

    /**
     * @var array
     */
    private $themeChoices;

    /**
     * @var array
     */
    private $moduleChoices;

    /**
     * @param TranslatorInterface $translator
     * @param array $locales
     * @param array $translationTypeChoices
     * @param array $emailContentTypeChoices
     * @param array $themeChoices
     * @param array $moduleChoices
     */
    public function __construct(
        TranslatorInterface $translator,
        array $locales,
        array $translationTypeChoices,
        array $emailContentTypeChoices,
        array $themeChoices,
        array $moduleChoices
    ) {
        parent::__construct($translator, $locales);
        $this->translationTypeChoices = $translationTypeChoices;
        $this->emailContentTypeChoices = $emailContentTypeChoices;
        $this->themeChoices = $themeChoices;
        $this->moduleChoices = $moduleChoices;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $noTheme = $this->trans('Core (no theme selected)', 'Admin.International.Feature');

        $builder
            ->add('translation_type', ChoiceType::class, [
                'choices' => $this->translationTypeChoices,
                'choice_translation_domain' => false,
            ])
            ->add('email_content_type', ChoiceType::class, [
                'choices' => $this->emailContentTypeChoices,
                'choice_translation_domain' => false,
            ])
            ->add('theme', ChoiceType::class, [
                'choices' => [$noTheme => 0] +
                $this->themeChoices,
                'choice_attr' => [
                    $noTheme => [
                        'class' => 'js-no-theme',
                    ],
                ],
                'choice_translation_domain' => false,
            ])
            ->add('module', ChoiceType::class, [
                'placeholder' => '---',
                'choices' => $this->moduleChoices,
                'choice_translation_domain' => false,
            ])
            ->add('language', ChoiceType::class, [
                'placeholder' => $this->trans('Language', 'Admin.Global'),
                'choices' => $this->getLocaleChoices(),
                'choice_translation_domain' => false,
            ]);
    }
}
