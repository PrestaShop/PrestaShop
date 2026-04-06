<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Form\Admin\Sell\Discount;

use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\DefaultLanguage;
use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\TypedRegex;
use PrestaShop\PrestaShop\Core\Domain\Discount\DiscountSettings;
use PrestaShopBundle\Form\Admin\Type\CardType;
use PrestaShopBundle\Form\Admin\Type\SwitchType;
use PrestaShopBundle\Form\Admin\Type\TextPreviewType;
use PrestaShopBundle\Form\Admin\Type\TranslatableType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;

class DiscountInformationType extends TranslatorAwareType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $discountType = $options['discount_type'];
        $builder
            ->add('discount_type', TextPreviewType::class, [
                'data' => $discountType,
                'label' => $this->trans('Discount Type', 'Admin.Catalog.Feature'),
                'required' => false,
            ])
            ->add('names', TranslatableType::class, [
                'label' => $this->trans('Discount Name', 'Admin.Catalog.Feature'),
                'label_help_box' => $this->trans('This will be displayed in the cart summary, as well as on the invoice.', 'Admin.Catalog.Help'),
                'required' => true,
                'type' => TextType::class,
                'constraints' => [
                    new DefaultLanguage(),
                ],
                'options' => [
                    'constraints' => [
                        new TypedRegex([
                            'type' => 'generic_name',
                        ]),
                        new Length([
                            'max' => DiscountSettings::MAX_NAME_LENGTH,
                            'maxMessage' => $this->trans(
                                'This field cannot be longer than %limit% characters',
                                'Admin.Notifications.Error',
                                ['%limit%' => DiscountSettings::MAX_NAME_LENGTH]
                            ),
                        ]),
                    ],
                ],
            ])
            ->add('description', TextareaType::class, [
                'label' => $this->trans('Discount description', 'Admin.Global'),
                'required' => false,
                'label_help_box' => $this->trans(
                    'For your eyes only. This will never be displayed to the customer.',
                    'Admin.Catalog.Help'
                ),
                'constraints' => [
                    new TypedRegex([
                        'type' => TypedRegex::CLEAN_HTML_NO_IFRAME,
                    ]),
                    new Length([
                        'max' => DiscountSettings::MAX_DESCRIPTION_LENGTH,
                        'maxMessage' => $this->trans(
                            'This field cannot be longer than %limit% characters',
                            'Admin.Notifications.Error',
                            ['%limit%' => DiscountSettings::MAX_DESCRIPTION_LENGTH]
                        ),
                    ]),
                ],
            ])
            ->add('active', SwitchType::class, [
                'label' => $this->trans('Enable Discount', 'Admin.Global'),
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);
        $resolver->setRequired([
            'discount_type',
        ]);
        $resolver->setAllowedTypes('discount_type', ['string']);
        $resolver->setDefaults([
            'label' => $this->trans('Discount information', 'Admin.Catalog.Feature'),
        ]);
    }

    public function getParent()
    {
        return CardType::class;
    }
}
