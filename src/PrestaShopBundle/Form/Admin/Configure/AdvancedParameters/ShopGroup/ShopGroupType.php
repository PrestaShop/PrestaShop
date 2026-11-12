<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Form\Admin\Configure\AdvancedParameters\ShopGroup;

use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\TypedRegex;
use PrestaShopBundle\Form\Admin\Type\ColorPickerType;
use PrestaShopBundle\Form\Admin\Type\SwitchType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class ShopGroupType extends TranslatorAwareType
{
    private const NAME_MAX_LENGTH = 64;

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => $this->trans('Name of the store group', 'Admin.Advparameters.Feature'),
                'constraints' => [
                    new NotBlank([
                        'message' => $this->trans(
                            'The %s field is required.',
                            'Admin.Notifications.Error',
                            [sprintf('"%s"', $this->trans('Name of the store group', 'Admin.Advparameters.Feature'))]
                        ),
                    ]),
                    new TypedRegex(['type' => TypedRegex::TYPE_GENERIC_NAME]),
                    new Length([
                        'max' => self::NAME_MAX_LENGTH,
                        'maxMessage' => $this->trans(
                            'This field cannot be longer than %limit% characters',
                            'Admin.Notifications.Error',
                            ['%limit%' => self::NAME_MAX_LENGTH]
                        ),
                    ]),
                ],
            ])
            ->add('color', ColorPickerType::class, [
                'label' => $this->trans('Color', 'Admin.Catalog.Feature'),
                'required' => false,
                'empty_data' => '',
                'help' => $this->trans('It will only be applied to this group of shops, each store will keep its individual color.', 'Admin.Shopparameters.Feature'),
            ])
            ->add('share_customer', SwitchType::class, [
                'label' => $this->trans('Share customers', 'Admin.Advparameters.Feature'),
                'disabled' => $options['sharing_options_locked'],
                'help' => $this->trans('Once this option is enabled, the shops in this group will share customers. If a customer registers in any one of these shops, the account will automatically be available in the others shops of this group.', 'Admin.Advparameters.Help')
                    . ' ' . $this->trans('Warning: you will not be able to disable this option once you have registered customers.', 'Admin.Advparameters.Help'),
            ])
            ->add('share_stock', SwitchType::class, [
                'label' => $this->trans('Share available quantities for sale', 'Admin.Advparameters.Feature'),
                'disabled' => $options['sharing_options_locked'],
                'help' => $this->trans('Share available quantities between shops of this group. When changing this option, all available products quantities will be reset to 0.', 'Admin.Advparameters.Feature'),
            ])
            ->add('share_order', SwitchType::class, [
                'label' => $this->trans('Share orders', 'Admin.Advparameters.Feature'),
                'disabled' => $options['sharing_options_locked'],
                'help' => $this->trans('Once this option is enabled (which is only possible if customers and available quantities are shared among shops), the customer\'s cart will be shared by all shops in this group. This way, any purchase started in one shop will be able to be completed in another shop from the same group.', 'Admin.Advparameters.Help')
                    . ' ' . $this->trans('Warning: You will not be able to disable this option once you\'ve started to accept orders.', 'Admin.Advparameters.Help'),
            ])
            ->add('active', SwitchType::class, [
                'label' => $this->trans('Status', 'Admin.Global'),
                'help' => $this->trans('Enable or disable this shop group?', 'Admin.Advparameters.Help'),
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);
        $resolver
            ->setDefault('sharing_options_locked', false)
            ->setAllowedTypes('sharing_options_locked', 'bool');
    }
}
