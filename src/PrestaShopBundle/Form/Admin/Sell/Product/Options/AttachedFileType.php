<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace PrestaShopBundle\Form\Admin\Sell\Product\Options;

use PrestaShopBundle\Form\Admin\Type\EntityItemType;
use PrestaShopBundle\Form\Admin\Type\TextPreviewType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\FormBuilderInterface;

class AttachedFileType extends TranslatorAwareType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->remove('name')
            ->remove('image')
            ->add('attachment_id', TextPreviewType::class, [
                'label' => $this->trans('ID', 'Admin.Global'),
            ])
            ->add('name', TextPreviewType::class, [
                'label' => $this->trans('Title', 'Admin.Global'),
            ])
            ->add('file_name', TextPreviewType::class, [
                'label' => $this->trans('File name', 'Admin.Global'),
            ])
            ->add('mime_type', TextPreviewType::class, [
                'label' => $this->trans('Type', 'Admin.Global'),
            ])
        ;
    }

    /**
     * {@inheritDoc}
     */
    public function getParent(): string
    {
        return EntityItemType::class;
    }
}
