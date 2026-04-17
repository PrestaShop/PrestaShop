<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Form\Admin\Improve\Shipping\Carrier;

use PrestaShopBundle\Form\Admin\Type\IconButtonType;
use PrestaShopBundle\Form\Admin\Type\NavigationTabType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class CarrierType extends TranslatorAwareType
{
    public function __construct(
        TranslatorInterface $translator,
        array $locales,
        protected readonly RouterInterface $router,
    ) {
        parent::__construct($translator, $locales);
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
        $builder
            ->add('general_settings', GeneralSettings::class, [
                'label' => $this->trans('General settings', 'Admin.Shipping.Feature'),
            ])
            ->add('shipping_settings', ShippingLocationsAndCostsType::class, [
                'label' => $this->trans('Shipping locations and costs', 'Admin.Shipping.Feature'),
            ])
            ->add('size_weight_settings', SizeWeightSettings::class, [
                'label' => $this->trans('Size and weight', 'Admin.Shipping.Feature'),
            ])
        ;
    }

    public function getParent()
    {
        return NavigationTabType::class;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            'form_theme' => '@PrestaShop/Admin/TwigTemplateForm/prestashop_ui_kit.html.twig',
            'label' => false,
            'footer_buttons' => [
                'cancel' => [
                    'type' => IconButtonType::class,
                    'options' => [
                        'label' => $this->trans('Cancel', 'Admin.Global'),
                        'type' => 'link',
                        'attr' => [
                            'href' => $this->router->generate('admin_carriers_index'),
                        ],
                    ],
                ],
                'submit' => [
                    'type' => SubmitType::class,
                    'options' => [
                        'label' => $this->trans('Save', 'Admin.Global'),
                    ],
                    'group' => 'right',
                ],
            ],
        ]);
    }
}
