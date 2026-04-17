<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Form\Admin\AdvancedParameters\Performance;

use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * This form class generates the "Media servers" form in Performance page.
 */
class MediaServersType extends TranslatorAwareType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('media_server_one', TextType::class, [
                'label' => $this->trans('Media server #1', 'Admin.Advparameters.Feature'),
                'help' => $this->trans('Name of the second domain of your shop, (e.g. myshop-media-server-1.com). If you do not have another domain, leave this field blank.', 'Admin.Advparameters.Help'),
                'empty_data' => '',
                'required' => false,
            ])
            ->add('media_server_two', TextType::class, [
                'label' => $this->trans('Media server #2', 'Admin.Advparameters.Feature'),
                'help' => $this->trans('Name of the third domain of your shop, (e.g. myshop-media-server-2.com). If you do not have another domain, leave this field blank.', 'Admin.Advparameters.Help'),
                'empty_data' => '',
                'required' => false,
            ])
            ->add('media_server_three', TextType::class, [
                'label' => $this->trans('Media server #3', 'Admin.Advparameters.Feature'),
                'help' => $this->trans('Name of the fourth domain of your shop, (e.g. myshop-media-server-3.com). If you do not have another domain, leave this field blank.', 'Admin.Advparameters.Help'),
                'empty_data' => '',
                'required' => false,
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'performance_media_servers_block';
    }
}
