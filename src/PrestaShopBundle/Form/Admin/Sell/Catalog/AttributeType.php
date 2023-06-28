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

namespace PrestaShopBundle\Form\Admin\Sell\Address;

use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Form type for attribute add/edit
 */
class AttributeType extends TranslatorAwareType
{
    private $attributeTypes;

    /**
     * AttributeType constructor
     *
     * @param TranslatorInterface $translator
     * @param array $locales
     */
    public function __construct(
        TranslatorInterface $translator,
        array $locales,
        array $attributeTypes
    ) {
        parent::__construct($translator, $locales);
        $this->attributeTypes = $attributeTypes;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name', TextType::class, [
            'label' => $this->trans('Name', 'Admin.Global'),
            'help' => $this->trans('Your internal name for this attribute.', 'Admin.Catalog.Help') . '&nbsp;' . $this->trans('Invalid characters:', 'Admin.Notifications.Info') . ' <>;=#{}',
            ])
            ->add('public_name', TextType::class, [
                'label' => $this->trans('Name', 'Admin.Catalog.Feature'),
                'help' => $this->trans('The public name for this attribute, displayed to the customers.', 'Admin.Catalog.Help') . '&nbsp;' . $this->trans('Invalid characters:', 'Admin.Notifications.Info') . ' <>;=#{}',
            ])
            ->add('attribute_type', ChoiceType::class, [
                'label' => $this->trans('Attribute type', 'Admin.Catalog.Feature'),
                'help' => $this->trans('The way the attribute\'s values will be presented to the customers in the product\'s page.', 'Admin.Catalog.Help'),
                'choices' => $this->attributeTypes,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'requiredFields' => [],
        ]);
    }
}
