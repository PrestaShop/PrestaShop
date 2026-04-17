<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Form\Admin\Improve\Shipping\Carrier\Type;

use PrestaShopBundle\Form\Admin\Type\IconButtonType;
use PrestaShopBundle\Form\Admin\Type\TextPreviewType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CostsZoneType extends TranslatorAwareType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('zoneId', HiddenType::class)
            ->add('deleteZone', IconButtonType::class, [
                'label' => false,
                'icon' => 'delete',
                'attr' => [
                    'class' => 'js-carrier-delete-zone',
                    'data-modal-title' => $this->trans('Delete Zone?', 'Admin.Shipping.Feature'),
                    'data-modal-confirm' => $this->trans('Delete', 'Admin.Actions'),
                    'data-modal-cancel' => $this->trans('Cancel', 'Admin.Actions'),
                ],
            ])
            ->add('zoneName', TextPreviewType::class, [
                'label' => false,
            ])
            ->add('ranges', CollectionType::class, [
                'prototype_name' => '__range__',
                'entry_type' => CostsRangeType::class,
                'label' => false,
                'required' => false,
                'allow_add' => true,
                'allow_delete' => true,
                'block_prefix' => 'carrier_ranges_costs_zone_ranges_collection',
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'label' => false,
            'form_theme' => '@PrestaShop/Admin/Improve/Shipping/Carriers/FormTheme/costs-range.html.twig',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'carrier_ranges_costs_zone';
    }
}
