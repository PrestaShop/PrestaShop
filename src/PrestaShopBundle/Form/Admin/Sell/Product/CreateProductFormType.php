<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Form\Admin\Sell\Product;

use PrestaShopBundle\Form\Admin\Type\ShopSelectorType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CreateProductFormType extends TranslatorAwareType
{
    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('type', ProductTypeType::class)
            ->add('shop_id', HiddenType::class)
            ->add('create', SubmitType::class, [
                'label' => $this->trans('Add new product', 'Admin.Catalog.Feature'),
                'row_attr' => [
                    'class' => 'text-right',
                ],
            ])
        ;

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $data = $event->getData();
            if (!empty($data['shop_id'])) {
                return;
            }

            // If no shop as pre-selected we use the shop selector input instead of a hidden one
            $form = $event->getForm();
            $form->add('shop_id', ShopSelectorType::class);
        });
    }

    /**
     * {@inheritDoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'required' => false,
            'label' => false,
            'attr' => [
                'data-modal-title' => $this->trans('Add new product', 'Admin.Catalog.Feature'),
            ],
            'form_theme' => '@PrestaShop/Admin/Sell/Catalog/Product/FormTheme/product.html.twig',
            'use_default_themes' => false,
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function getBlockPrefix()
    {
        return 'create_product';
    }
}
