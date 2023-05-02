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

namespace PrestaShopBundle\Form\Admin\Sell\Catalog;

use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\TypedRegex;
use PrestaShop\PrestaShop\Core\ConstraintValidator\TypedRegexValidator;
use PrestaShop\PrestaShop\Core\Domain\AttributeGroup\ValueObject\AttributeGroupType as GroupType;
use PrestaShopBundle\Form\Admin\Type\ShopChoiceTreeType;
use PrestaShopBundle\Form\Admin\Type\TranslatableType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Form type for attribute add/edit
 */
class AttributeGroupType extends TranslatorAwareType
{
    /**
     * @var bool
     */
    private $isMultistoreEnabled;

    /**
     * @param TranslatorInterface $translator
     * @param array $locales
     * @param bool $isMultistoreEnabled
     */
    public function __construct(
        TranslatorInterface $translator,
        array $locales,
        bool $isMultistoreEnabled
    ) {
        parent::__construct($translator, $locales);

        $this->isMultistoreEnabled = $isMultistoreEnabled;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TranslatableType::class, [
                'type' => TextType::class,
                'label' => $this->trans('Name', 'Admin.Global'),
                'options' => [
                    'constraints' => [
                        new TypedRegex([
                            'type' => TypedRegex::TYPE_CATALOG_NAME,
                        ]),
                    ],
                ],
                'help' => $this->trans('Your internal name for this attribute.', 'Admin.Catalog.Help')
                    . '&nbsp;' . $this->trans('Invalid characters:', 'Admin.Notifications.Info')
                    . ' ' . TypedRegexValidator::CATALOG_CHARS,
            ])
            ->add('public_name', TranslatableType::class, [
                'type' => TextType::class,
                'label' => $this->trans('Public name', 'Admin.Catalog.Feature'),
                'options' => [
                    'constraints' => [
                        new TypedRegex([
                            'type' => TypedRegex::TYPE_CATALOG_NAME,
                        ]),
                    ],
                ],
                'help' => $this->trans('Your internal name for this attribute.', 'Admin.Catalog.Help')
                    . '&nbsp;' . $this->trans('Invalid characters:', 'Admin.Notifications.Info')
                    . ' ' . TypedRegexValidator::CATALOG_CHARS,
            ])
            ->add('group_type', ChoiceType::class, [
                'label' => $this->trans('Attribute type', 'Admin.Catalog.Feature'),
                'help' => $this->trans('The way the attribute\'s values will be presented to the customers in the product\'s page.', 'Admin.Catalog.Help'),
                'choices' => [
                    $this->trans('Drop-down list', 'Admin.Global') => GroupType::ATTRIBUTE_GROUP_TYPE_SELECT,
                    $this->trans('Radio buttons', 'Admin.Global') => GroupType::ATTRIBUTE_GROUP_TYPE_RADIO,
                    $this->trans('Color or texture', 'Admin.Catalog.Feature') => GroupType::ATTRIBUTE_GROUP_TYPE_COLOR,
                ],
            ])->add('shop_association', ShopChoiceTreeType::class, [
                'label' => $this->trans('Shop association', 'Admin.Global'),
                'required' => false,
                'constraints' => [
                    new NotBlank([
                        'message' => $this->trans(
                            'This field cannot be empty.', 'Admin.Notifications.Error'
                        ),
                    ]),
                ],
            ]);

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'requiredFields' => [],
        ]);
    }
}
