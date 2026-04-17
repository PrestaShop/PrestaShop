<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Form\Admin\Sell\Discount;

use PrestaShopBundle\Form\Admin\Type\CarrierChoiceType;
use PrestaShopBundle\Form\Admin\Type\CountryChoiceType;
use PrestaShopBundle\Form\Admin\Type\ToggleChildrenChoiceType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\When;

class DeliveryConditionsType extends TranslatorAwareType
{
    public const NONE = 'none';
    public const CARRIERS = 'carriers';
    public const COUNTRY = 'country';

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(self::NONE, HiddenType::class, [
                'label' => $this->trans('None', 'Admin.Catalog.Feature'),
            ])
            ->add(self::CARRIERS, CarrierChoiceType::class, [
                'label' => $this->trans('Specific carriers', 'Admin.Catalog.Feature'),
                'multiple' => true,
                'attr' => [
                    'data-placeholder' => $this->trans('Select carriers', 'Admin.Catalog.Feature'),
                ],
                'constraints' => [
                    new When(
                        expression: sprintf(
                            'this.getParent().get("children_selector").getData() === "%s"',
                            self::CARRIERS
                        ),
                        constraints: new NotBlank(),
                    ),
                ],
            ])
            ->add(self::COUNTRY, CountryChoiceType::class, [
                'label' => $this->trans('Specific countries', 'Admin.Catalog.Feature'),
                'multiple' => true,
                'expanded' => false,
                'with_logo_attr' => true,
                'attr' => [
                    'data-placeholder' => $this->trans('Select countries', 'Admin.Catalog.Feature'),
                ],
                'constraints' => [
                    new When(
                        expression: sprintf(
                            'this.getParent().get("children_selector").getData() === "%s"',
                            self::COUNTRY
                        ),
                        constraints: new NotBlank(),
                    ),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);
        $resolver->setDefaults([
            'label' => $this->trans('On delivery', 'Admin.Catalog.Feature'),
        ]);
    }

    public function getParent()
    {
        return ToggleChildrenChoiceType::class;
    }
}
