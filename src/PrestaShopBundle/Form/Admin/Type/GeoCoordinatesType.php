<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */
declare(strict_types=1);

namespace PrestaShopBundle\Form\Admin\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Form\Extension\Core\Type\NumberType;

/**
 * This form class is responsible to create a geolocation latitude/longitude coordinates field.
 */
class GeoCoordinatesType extends AbstractType
{
    /**
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * @param TranslatorInterface $translator
     */
    public function __construct(
        TranslatorInterface $translator
    ) {
        $this->translator = $translator;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('latitude', NumberType::class, [
                'required' => false,
                'label' => $options['label_latitude'],
                'attr' => [
                    'placeholder' => $this->translator->trans('-0.12345', [], 'Admin.Global'),
                    'class' => 'latitude',
                ],
            ])
            ->add('longitude', NumberType::class, [
                'required' => false,
                'label' => $options['label_longitude'],
                'attr' => [
                    'placeholder' => $this->translator->trans('-0.12345', [], 'Admin.Global'),
                    'class' => 'longitude',
                ],
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'label_latitude' => $this->translator->trans('Latitude', [], 'Admin.Global'),
            'label_longitude' => $this->translator->trans('Longitude', [], 'Admin.Global'),
            'compound' => true,
            'inherit_data' => true,
        ]);
        $resolver
            ->setAllowedTypes('label_latitude', 'string')
            ->setAllowedTypes('label_longitude', 'string')
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'geo_coordinates';
    }
}
