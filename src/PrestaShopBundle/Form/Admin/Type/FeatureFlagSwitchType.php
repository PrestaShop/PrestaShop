<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Form\Admin\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Displays a switch (ON / OFF by default) for feature flags.
 */
class FeatureFlagSwitchType extends AbstractType
{
    public const TRANS_DOMAIN = 'Admin.Global';

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'block_prefix' => 'feature_flag',
            'form_theme' => '@PrestaShop/Admin/Configure/AdvancedParameters/FeatureFlag/FormTheme/feature_flag_form.html.twig',
            'types' => [],
            'used_type' => null,
            'forced_by_env' => false,
        ]);
        $resolver->setAllowedTypes('types', 'array');
        $resolver->setAllowedTypes('used_type', ['null', 'string']);
        $resolver->setAllowedTypes('forced_by_env', 'bool');
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['types'] = $options['types'];
        $view->vars['used_type'] = $options['used_type'];
        $view->vars['forced_by_env'] = $options['forced_by_env'];
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return SwitchType::class;
    }
}
