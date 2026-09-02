<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace PrestaShopBundle\Form\Admin\Sell\Product\Pricing;

use PrestaShopBundle\Form\Admin\Type\IconButtonType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SpecificPricesType extends TranslatorAwareType
{
    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('add_specific_price_btn', IconButtonType::class, [
                'label' => $this->trans('Add a specific price', 'Admin.Catalog.Feature'),
                'attr' => [
                    'class' => 'js-add-specific-price-btn btn btn-primary',
                    'data-modal-title' => $this->trans('Add new specific price', 'Admin.Catalog.Help'),
                    'data-confirm-button-label' => $this->trans('Save and publish', 'Admin.Actions'),
                    'data-cancel-button-label' => $this->trans('Cancel', 'Admin.Actions'),
                ],
                'icon' => 'add_circle',
            ])
        ;
    }

    /**
     * {@inheritDoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);
        $resolver->setDefaults([
            'form_theme' => '@PrestaShop/Admin/Sell/Catalog/Product/FormTheme/specific_prices.html.twig',
        ]);
    }
}
