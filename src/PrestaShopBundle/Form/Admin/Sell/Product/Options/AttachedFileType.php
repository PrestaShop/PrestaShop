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

use PrestaShopBundle\Form\Admin\Type\TextPreviewType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;

class AttachedFileType extends TranslatorAwareType
{
    public const ID_PLACEHOLDER = '__attachment_id__';
    public const NAME_PLACEHOLDER = '__name__';
    public const FILE_NAME_PLACEHOLDER = '__file_name__';
    public const MIME_TYPE_PLACEHOLDER = '__mime_type__';

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('attachment_id', HiddenType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'attachment-id-input',
                ],
                'default_empty_data' => self::ID_PLACEHOLDER,
            ])
            ->add('name', TextPreviewType::class, [
                'label' => $this->trans('Title', 'Admin.Global'),
                'default_empty_data' => self::NAME_PLACEHOLDER,
            ])
            ->add('file_name', TextPreviewType::class, [
                'label' => $this->trans('File name', 'Admin.Global'),
                'default_empty_data' => self::FILE_NAME_PLACEHOLDER,
            ])
            ->add('mime_type', TextPreviewType::class, [
                'label' => $this->trans('Type', 'Admin.Global'),
                'default_empty_data' => self::MIME_TYPE_PLACEHOLDER,
            ])
        ;
    }

    public function getBlockPrefix()
    {
        return 'entity_item';
    }
}
