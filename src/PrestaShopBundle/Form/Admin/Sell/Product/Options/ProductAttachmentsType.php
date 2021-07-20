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

use PrestaShopBundle\Form\Admin\Type\EntitySearchInputType;
use PrestaShopBundle\Form\Admin\Type\IconButtonType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Translation\TranslatorInterface;

class ProductAttachmentsType extends TranslatorAwareType
{
    /**
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;

    public function __construct(
        TranslatorInterface $translator,
        array $locales,
        UrlGeneratorInterface $urlGenerator
    ) {
        parent::__construct($translator, $locales);
        $this->urlGenerator = $urlGenerator;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('attached_files', EntitySearchInputType::class, [
                'entry_type' => AttachedFileType::class,
                'layout' => EntitySearchInputType::TABLE_LAYOUT,
                'prototype_mapping' => [
                    'attachment_id' => AttachedFileType::ID_PLACEHOLDER,
                    'name' => AttachedFileType::NAME_PLACEHOLDER,
                    'file_name' => AttachedFileType::FILE_NAME_PLACEHOLDER,
                    'mime_type' => AttachedFileType::MIME_TYPE_PLACEHOLDER,
                ],
                'required' => false,
                'label' => false,
                'remote_url' => $this->urlGenerator->generate('admin_attachments_search', ['searchPhrase' => '__QUERY__']),
                'placeholder' => $this->trans('Search file', 'Admin.Catalog.Feature'),
            ])
            ->add('add_attachment_btn', IconButtonType::class, [
                'label' => $this->trans('Add new file', 'Admin.Catalog.Feature'),
                'icon' => 'add_circle',
                'type' => 'link',
                'attr' => [
                    'data-success-create-message' => sprintf('%s %s',
                        $this->trans(
                            'Attachment was successfully created and added to the selection.',
                            'Admin.Catalog.Feature'
                        ),
                        $this->trans(
                            'This window is gonna close automatically.',
                            'Admin.Glocal'
                        )
                    ),
                    'class' => 'btn-outline-secondary add-attachment',
                    'href' => $this->urlGenerator->generate('admin_attachments_create', [
                        'liteDisplaying' => true,
                        'saveAndStay' => true,
                    ]),
                ],
            ])
        ;
    }
}
