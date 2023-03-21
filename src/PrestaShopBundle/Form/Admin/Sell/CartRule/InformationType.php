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

namespace PrestaShopBundle\Form\Admin\Sell\CartRule;

use PrestaShopBundle\Form\Admin\Type\SwitchType;
use PrestaShopBundle\Form\Admin\Type\TranslatableType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class InformationType extends TranslatorAwareType
{
    public function __construct(
        TranslatorInterface $translator,
        array $locales
    ) {
        parent::__construct($translator, $locales);
    }

    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TranslatableType::class, [
                'label' => $this->trans('Name', 'Admin.Global'),
            ])
            ->add('description', TextareaType::class, [
                'label' => $this->trans('Description', 'Admin.Global'),
                'required' => false,
            ])
            ->add('code', TextType::class, [
                //@todo: implement some widget generating random code
                'label' => $this->trans('Code', 'Admin.Global'),
                'required' => false,
                'help' => $this->trans(
                    'Caution! If you leave this field blank, the rule will automatically be applied to benefiting customers.',
                    'Admin.Catalog.Help'
                ),
            ])
            ->add('highlight', SwitchType::class, [
                'label' => $this->trans('Highlight', 'Admin.Catalog.Feature'),
                'required' => false,
            ])
            ->add('partial_use', SwitchType::class, [
                'label' => $this->trans('Partial use', 'Admin.Global'),
                'required' => false,
            ])
            ->add('priority', NumberType::class, [
                'label' => $this->trans('Priority', 'Admin.Catalog.Feature'),
                'required' => false,
            ])
            ->add('enabled', SwitchType::class, [
                'label' => $this->trans('Status', 'Admin.Global'),
                'required' => false,
            ])
        ;
    }
}
