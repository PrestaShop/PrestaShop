<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Form\Admin\Sell\Product;

use PrestaShopBundle\Form\Admin\Type\ButtonCollectionType;
use PrestaShopBundle\Form\Admin\Type\ShopSelectorType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * This form type is used to copy data from one shop to some others, you can also unselect/remove some
 * shops. The content of the shop is based on the product initial shops and the whole list of selectable
 * shops.
 */
class ProductShopsType extends TranslatorAwareType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('source_shop_id', HiddenType::class)
            ->add('initial_shops', CollectionType::class, [
                'entry_type' => HiddenType::class,
                'label' => false,
            ])
            ->add('selected_shops', ShopSelectorType::class, [
                'multiple' => true,
            ])
            ->add('buttons', ButtonCollectionType::class, [
                'buttons' => [
                    'cancel' => [
                        'type' => ButtonType::class,
                        'group' => 'left',
                        'options' => [
                            'label' => $this->trans('Cancel', 'Admin.Global'),
                            'attr' => [
                                'class' => 'btn-secondary',
                            ],
                        ],
                    ],
                    'submit' => [
                        'type' => SubmitType::class,
                        'group' => 'right',
                        'options' => [
                            'label' => $this->trans('Save', 'Admin.Global'),
                        ],
                    ],
                ],
                'justify_content' => 'flex-end',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);
        $resolver->setDefaults([
            'label' => false,
            'form_theme' => '@PrestaShop/Admin/Sell/Catalog/Product/FormTheme/product.html.twig',
            'use_default_themes' => false,
        ]);
    }
}
