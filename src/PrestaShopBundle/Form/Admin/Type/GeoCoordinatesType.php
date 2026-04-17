<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace PrestaShopBundle\Form\Admin\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * This form class is responsible to create a geolocation latitude/longitude coordinates field.
 */
class GeoCoordinatesType extends AbstractType
{
    /**
     * @param TranslatorInterface $translator
     */
    public function __construct(
        private readonly TranslatorInterface $translator
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
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
    public function configureOptions(OptionsResolver $resolver): void
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
    public function getBlockPrefix(): string
    {
        return 'geo_coordinates';
    }
}
