<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Form\Admin\Sell\Discount;

use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\TypedRegex;
use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\UniqueDiscountCode;
use PrestaShopBundle\Form\Admin\Type\GeneratableTextType;
use PrestaShopBundle\Form\Admin\Type\ToggleChildrenChoiceType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\When;

class DiscountUsabilityModeType extends TranslatorAwareType
{
    public const AUTO_MODE = 'auto';
    public const CODE_MODE = 'code';
    protected const GENERATED_CODE_LENGTH = 8;

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(self::AUTO_MODE, HiddenType::class, [
                'label' => $this->trans('Create automatic discount', 'Admin.Catalog.Feature'),
            ])
            ->add(self::CODE_MODE, GeneratableTextType::class, [
                'label' => $this->trans('Generate discount code', 'Admin.Catalog.Feature'),
                'required' => false,
                'generated_value_length' => self::GENERATED_CODE_LENGTH,
                'constraints' => [
                    new When(
                        expression: sprintf(
                            'this.getParent().get("children_selector").getData() === "%s"',
                            self::CODE_MODE,
                        ),
                        constraints: [
                            new NotBlank(),
                            new TypedRegex(TypedRegex::TYPE_DISCOUNT_CODE),
                            new UniqueDiscountCode(),
                        ],
                    ),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);
        $resolver->setDefaults([
            'required' => false,
        ]);
    }

    public function getParent()
    {
        return ToggleChildrenChoiceType::class;
    }
}
