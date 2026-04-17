<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Form\Admin\Configure\ShopParameters\ProductPreferences;

use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class generates "Pagination" form
 * in "Configure > Shop Parameters > Product Settings" page.
 */
class PaginationType extends TranslatorAwareType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('products_per_page', IntegerType::class, [
                'label' => $this->trans(
                    'Products per page',
                    'Admin.Shopparameters.Feature'
                ),
                'help' => $this->trans('Number of products displayed per page. Default is 12', 'Admin.Shopparameters.Help'),
                'required' => false,
            ])
            ->add('default_order_by', ChoiceType::class, [
                'label' => $this->trans(
                    'Default order by',
                    'Admin.Shopparameters.Feature'
                ),
                'help' => $this->trans(
                    'The default order in which products are displayed in listings. The available sorting methods differ based on the page type.',
                    'Admin.Shopparameters.Help'
                ),
                'choices' => [
                    'Product name' => 0,
                    'Product price' => 1,
                    'Product add date' => 2,
                    'Product modified date' => 3,
                    'Position inside category' => 4,
                    'Brand' => 5,
                    'Product quantity' => 6,
                    'Product reference' => 7,
                    'Product sales' => 8,
                ],
                'required' => false,
                'placeholder' => false,
            ])
            ->add('default_order_way', ChoiceType::class, [
                'label' => $this->trans(
                    'Default order method',
                    'Admin.Shopparameters.Feature'
                ),
                'choices' => [
                    'Ascending' => 0,
                    'Descending' => 1,
                ],
                'choice_translation_domain' => 'Admin.Global',
                'required' => false,
                'placeholder' => false,
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'translation_domain' => 'Admin.Shopparameters.Feature',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'product_preferences_pagination_block';
    }
}
