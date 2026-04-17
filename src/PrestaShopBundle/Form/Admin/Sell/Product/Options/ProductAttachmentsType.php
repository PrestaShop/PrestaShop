<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
