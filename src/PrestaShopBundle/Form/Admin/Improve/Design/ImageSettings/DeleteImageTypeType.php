<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Form\Admin\Improve\Design\ImageSettings;

use PrestaShopBundle\Form\Admin\Type\SwitchType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\FormBuilderInterface;

class DeleteImageTypeType extends TranslatorAwareType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('delete_images_files_too', SwitchType::class, [
                'label' => $this->trans('Delete the images linked to this image setting', 'Admin.Design.Notification'),
                'required' => false,
                'show_choices' => false,
                'inline_switch' => true,
                'data' => false,
            ])
        ;
    }
}
