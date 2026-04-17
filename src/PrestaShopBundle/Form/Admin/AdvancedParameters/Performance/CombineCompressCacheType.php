<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Form\Admin\AdvancedParameters\Performance;

use PrestaShopBundle\Form\Admin\Type\SwitchType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * This form class generates the "Combine Compress Cache" form in Performance page.
 */
class CombineCompressCacheType extends TranslatorAwareType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('smart_cache_css', SwitchType::class, [
                'label' => $this->trans('Smart cache for CSS', 'Admin.Advparameters.Feature'),
            ])
            ->add('smart_cache_js', SwitchType::class, [
                'label' => $this->trans('Smart cache for JavaScript', 'Admin.Advparameters.Feature'),
            ])
            ->add('apache_optimization', SwitchType::class, [
                'label' => $this->trans('Apache optimization', 'Admin.Advparameters.Feature'),
                'help' => $this->trans('This will add directives to your .htaccess file, which should improve caching and compression.', 'Admin.Advparameters.Help'),
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'performance_ccc_block';
    }
}
