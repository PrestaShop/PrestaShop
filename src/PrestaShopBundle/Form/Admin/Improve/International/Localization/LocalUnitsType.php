<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Form\Admin\Improve\International\Localization;

use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class LocalUnitsType is responsible for building 'Improve > International > Localization' page
 * 'Local units' form.
 */
class LocalUnitsType extends TranslatorAwareType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('weight_unit', TextType::class, [
                'label' => $this->trans(
                    'Weight unit',
                    'Admin.International.Feature'
                ),
                'help' => $this->trans(
                    'The default weight unit for your shop (e.g. "kg" for kilograms, "lbs" for pound-mass, etc.).',
                    'Admin.International.Help'
                ),
            ])
            ->add('distance_unit', TextType::class, [
                'label' => $this->trans(
                    'Distance unit',
                    'Admin.International.Feature'
                ),
                'help' => $this->trans(
                    'The default distance unit for your shop (e.g. "km" for kilometer, "mi" for mile, etc.).',
                    'Admin.International.Help'
                ),
            ])
            ->add('volume_unit', TextType::class, [
                'label' => $this->trans(
                    'Volume unit',
                    'Admin.International.Feature'
                ),
                'help' => $this->trans(
                    'The default volume unit for your shop (e.g. "L" for liter, "gal" for gallon, etc.).',
                    'Admin.International.Help'
                ),
            ])
            ->add('dimension_unit', TextType::class, [
                'label' => $this->trans(
                    'Dimension unit',
                    'Admin.International.Feature'
                ),
                'help' => $this->trans(
                    'The default dimension unit for your shop (e.g. "cm" for centimeter, "in" for inch, etc.).',
                    'Admin.International.Help'
                ),
            ]);
    }
}
