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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
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
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class ProductAttachmentsType extends TranslatorAwareType
{
    /**
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;

    /**
     * @param TranslatorInterface $translator
     * @param array $locales
     * @param UrlGeneratorInterface $urlGenerator
     */
    public function __construct(
        TranslatorInterface $translator,
        array $locales,
        UrlGeneratorInterface $urlGenerator
    ) {
        parent::__construct($translator, $locales);
        $this->urlGenerator = $urlGenerator;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('attached_files', EntitySearchInputType::class, [
                'entry_type' => AttachedFileType::class,
                'layout' => EntitySearchInputType::TABLE_LAYOUT,
                'required' => false,
                'label' => false,
                'remote_url' => $this->urlGenerator->generate('admin_attachments_search', ['searchPhrase' => '__QUERY__']),
                'placeholder' => $this->trans('Search file', 'Admin.Catalog.Help'),
                'empty_state' => $this->trans('No files attached', 'Admin.Catalog.Feature'),
                'identifier_field' => 'attachment_id',
            ])
            ->add('add_attachment_btn', IconButtonType::class, [
                'label' => $this->trans('Add new file', 'Admin.Catalog.Feature'),
                'icon' => 'add_circle',
                'type' => 'link',
                'attr' => [
                    'data-success-create-message' => $this->trans(
                        'The file was successfully added.',
                        'Admin.Catalog.Notification'
                    ),
                    'data-modal-title' => $this->trans('Add new file', 'Admin.Catalog.Feature'),
                    'class' => 'btn-outline-secondary add-attachment',
                    'href' => $this->urlGenerator->generate('admin_attachments_create', [
                        'liteDisplaying' => true,
                        'saveAndStay' => true,
                    ]),
                ],
            ])
        ;
    }

    /**
     * {@inheritDoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);
        $resolver->setDefaults([
            'label' => $this->trans('Attached files', 'Admin.Catalog.Feature'),
            'label_tag_name' => 'h3',
            'label_help_box' => $this->trans('Instructions, size guide, or any file you want to add to a product.', 'Admin.Catalog.Help'),
            'label_subtitle' => $this->trans('Customers can download these files on the product page.', 'Admin.Catalog.Help'),
            'external_link' => [
                'text' => $this->trans('[1]Manage all files[/1]', 'Admin.Catalog.Feature'),
                'href' => $this->urlGenerator->generate('admin_attachments_index'),
                'position' => 'prepend',
                'attr' => [
                    'class' => 'pt-0',
                ],
            ],
        ]);
    }
}
