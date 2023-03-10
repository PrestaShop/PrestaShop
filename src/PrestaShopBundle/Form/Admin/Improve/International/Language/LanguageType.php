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

namespace PrestaShopBundle\Form\Admin\Improve\International\Language;

use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\TypedRegex;
use PrestaShopBundle\Form\Admin\Type\ShopChoiceTreeType;
use PrestaShopBundle\Form\Admin\Type\SwitchType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Builds language's add/edit form
 */
class LanguageType extends TranslatorAwareType
{
    private const MAX_NAME_LENGTH = 32;

    /**
     * @var bool
     */
    private $isMultistoreFeatureActive;

    /**
     * LanguageType constructor.
     *
     * @param TranslatorInterface $translator
     * @param array $locales
     * @param bool $isMultistoreFeatureActive
     */
    public function __construct(TranslatorInterface $translator, array $locales, bool $isMultistoreFeatureActive)
    {
        parent::__construct($translator, $locales);
        $this->isMultistoreFeatureActive = $isMultistoreFeatureActive;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label' => $this->trans('Name', 'Admin.Global'),
                'attr' => [
                    'maxLength' => self::MAX_NAME_LENGTH,
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => $this->trans('This field cannot be empty.', 'Admin.Notifications.Error'),
                    ]),
                    new TypedRegex([
                        'type' => 'generic_name',
                    ]),
                ],
            ])
            ->add('iso_code', TextType::class, [
                'attr' => [
                    'maxLength' => 2,
                ],
                'label' => $this->trans('ISO code', 'Admin.International.Feature'),
                'help' => $this->trans('Two-letter ISO code (e.g. FR, EN, DE).', 'Admin.International.Help'),
                'constraints' => [
                    new NotBlank([
                        'message' => $this->trans('This field cannot be empty.', 'Admin.Notifications.Error'),
                    ]),
                    new TypedRegex([
                        'type' => 'language_iso_code',
                    ]),
                ],
            ])
            ->add('tag_ietf', TextType::class, [
                'attr' => [
                    'maxLength' => 5,
                ],
                'label' => $this->trans('Language code', 'Admin.International.Feature'),
                'help' => $this->trans('IETF language tag (e.g. en-US, pt-BR).', 'Admin.International.Help'),
                'constraints' => [
                    new NotBlank([
                        'message' => $this->trans('This field cannot be empty.', 'Admin.Notifications.Error'),
                    ]),
                    new TypedRegex([
                        'type' => 'language_code',
                    ]),
                ],
            ])
            ->add('short_date_format', TextType::class, [
                'label' => $this->trans('Date format', 'Admin.International.Feature'),
                'help' => $this->trans('Short date format (e.g., Y-m-d).', 'Admin.International.Help'),
                'constraints' => [
                    new NotBlank([
                        'message' => $this->trans('This field cannot be empty.', 'Admin.Notifications.Error'),
                    ]),
                    new Regex([
                        // We can't really check if this is valid or not,
                        // because this is a string and you can write whatever you want in it.
                        // That's why only < et > are forbidden (HTML).
                        'pattern' => '/^[^<>]+$/',
                        'message' => $this->trans('This field is invalid.', 'Admin.Notifications.Error'),
                    ]),
                ],
            ])
            ->add('full_date_format', TextType::class, [
                'label' => $this->trans('Date format (full)', 'Admin.International.Feature'),
                'help' => $this->trans('Full date format (e.g., Y-m-d H:i:s).', 'Admin.International.Help'),
                'constraints' => [
                    new NotBlank([
                        'message' => $this->trans('This field cannot be empty.', 'Admin.Notifications.Error'),
                    ]),
                    new Regex([
                        'pattern' => '/^[^<>]+$/',
                        'message' => $this->trans('This field is invalid.', 'Admin.Notifications.Error'),
                    ]),
                ],
            ])
            ->add('flag_image', FileType::class, [
                'attr' => [
                    'class' => 'type-file',
                ],
                'label' => $this->trans('Flag', 'Admin.International.Feature'),
                'help' => $this->trans('Upload the country flag from your computer.', 'Admin.International.Help'),
                'required' => !$options['is_for_editing'],
                'constraints' => [
                    new Image([
                        'mimeTypesMessage' => $this->trans('This field is invalid.', 'Admin.Notifications.Error'),
                    ]),
                ],
            ])
            ->add('no_picture_image', FileType::class, [
                'label' => $this->trans('"No-picture" image', 'Admin.International.Feature'),
                'help' => $this->trans('Image is displayed when no picture is found.', 'Admin.International.Help'),
                'required' => !$options['is_for_editing'],
                'constraints' => [
                    new Image([
                        'mimeTypesMessage' => $this->trans('This field is invalid.', 'Admin.Notifications.Error'),
                    ]),
                ],
            ])
            ->add('is_rtl', SwitchType::class, [
                'label' => $this->trans('RTL language', 'Admin.International.Feature'),
                'help' => $this->trans(
                    'Enable if this language is read from right to left.',
                    'Admin.International.Help'
                ) . $this->trans(
                    '(Experimental: your theme must be compliant with RTL languages).',
                    'Admin.International.Help'
                ),
                'required' => false,
            ])
            ->add('is_active', SwitchType::class, [
                'label' => $this->trans('Status', 'Admin.Global'),
                'help' => $this->trans('Activate this language.', 'Admin.International.Help'),
                'required' => false,
            ])
        ;

        if ($this->isMultistoreFeatureActive) {
            $builder->add('shop_association', ShopChoiceTreeType::class, [
                'label' => $this->trans('Store association', 'Admin.Global'),
                'required' => false,
                'constraints' => [
                    new NotBlank([
                        'message' => $this->trans('This field cannot be empty.', 'Admin.Notifications.Error'),
                    ]),
                ],
            ]);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                // if form is used for editing
                // then some fields are optional
                'is_for_editing' => false,
            ])
            ->setAllowedTypes('is_for_editing', 'bool')
        ;
    }
}
