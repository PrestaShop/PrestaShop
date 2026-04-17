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
 * This form class generates the "Memcache server" form in Performance page.
 */
class MemcacheServerType extends TranslatorAwareType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('memcache_ip', TextType::class, [
                'label' => $this->trans('IP Address', 'Admin.Advparameters.Feature'),
                'empty_data' => '',
                'required' => false,
            ])
            ->add('memcache_port', TextType::class, [
                'label' => $this->trans('Port', 'Admin.Advparameters.Feature'),
                'empty_data' => '',
                'required' => false,
            ])
            ->add('memcache_weight', TextType::class, [
                'label' => $this->trans('Weight', 'Admin.Advparameters.Feature'),
                'empty_data' => '',
                'required' => false,
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'performance_memcache_server_block';
    }
}
