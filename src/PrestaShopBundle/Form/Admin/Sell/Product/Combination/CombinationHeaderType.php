<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Form\Admin\Sell\Product\Combination;

use PrestaShopBundle\Form\Admin\Type\TextPreviewType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CombinationHeaderType extends TranslatorAwareType
{
    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
        $builder
            ->add('name', TextPreviewType::class, [
                'label' => false,
                'prefix' => $this->trans('Edit combination: ', 'Admin.Catalog.Feature'),
                'row_attr' => [
                    'class' => 'combination-name-row',
                ],
            ])
            ->add('is_default', CheckboxType::class, [
                'label' => $this->trans('Set as default combination', 'Admin.Catalog.Feature'),
                'row_attr' => [
                    'class' => 'combination-default-row',
                ],
                'modify_all_shops' => true,
            ])
        ;
    }

    /**
     * {@inheritDoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);
        $resolver
            ->setDefaults([
                'label' => false,
                'required' => false,
                'row_attr' => [
                    'class' => 'combination-header-row',
                ],
            ])
        ;
    }
}
