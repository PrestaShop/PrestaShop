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

namespace PrestaShopBundle\Form\Admin\Sell\Product\Options;

use PrestaShopBundle\Form\Admin\Sell\Attachment\AttachmentType;
use PrestaShopBundle\Form\Admin\Type\IconButtonType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;

class ProductAttachmentType extends TranslatorAwareType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('attachment_ids', CollectionType::class, [
                'entry_type' => HiddenType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'prototype_name' => '__ATTACHMENT_INDEX__',
            ])
            ->add('new_attachment', AttachmentType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'd-none',
                ],
            ])
            ->add('add_attachment_btn', IconButtonType::class, [
                'label' => $this->trans('Attach a new file', 'Admin.Catalog.Feature'),
                'icon' => 'add_circle',
                'attr' => [
                    'class' => 'btn-outline-secondary add-attachment-btn',
                ],
            ])
        ;
    }
}
