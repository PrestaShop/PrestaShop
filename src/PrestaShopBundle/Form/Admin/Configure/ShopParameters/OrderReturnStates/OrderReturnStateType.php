<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace PrestaShopBundle\Form\Admin\Configure\ShopParameters\OrderReturnStates;

use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\DefaultLanguage;
use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\TypedRegex;
use PrestaShop\PrestaShop\Core\Domain\OrderReturnState\OrderReturnStateSettings;
use PrestaShopBundle\Form\Admin\Type\ColorPickerType;
use PrestaShopBundle\Form\Admin\Type\TranslatableType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;

/**
 * Type is used to created form for order return state add/edit actions
 */
class OrderReturnStateType extends TranslatorAwareType
{
    protected const NAME_CHARS = '!<>,;?=+()@#"{}_$%:';

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TranslatableType::class, [
                'label' => $this->trans('Status name', 'Admin.Shopparameters.Feature'),
                'help' => sprintf(
                    '%s %s %s',
                    $this->trans('Status name', 'Admin.Shopparameters.Feature'),
                    $this->trans('Invalid characters: numbers and', 'Admin.Shopparameters.Feature'),
                    static::NAME_CHARS
                ),
                'type' => TextType::class,
                'constraints' => [
                    new DefaultLanguage(),
                ],
                'options' => [
                    'constraints' => [
                        new TypedRegex([
                            'type' => TypedRegex::TYPE_GENERIC_NAME,
                        ]),
                        new Length([
                            'max' => OrderReturnStateSettings::NAME_MAX_LENGTH,
                            'maxMessage' => $this->trans(
                                'This field cannot be longer than %limit% characters',
                                'Admin.Notifications.Error',
                                [
                                    '%limit%' => OrderReturnStateSettings::NAME_MAX_LENGTH,
                                ]
                            ),
                        ]),
                    ],
                ],
            ])
            ->add('color', ColorPickerType::class, [
                'label' => $this->trans('Color', 'Admin.Shopparameters.Feature'),
                'help' => $this->trans('Status will be highlighted in this color. HTML colors only.', 'Admin.Shopparameters.Help'),
                'required' => false,
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'translation_domain' => 'Admin.Shopparameters.Feature',
            ])
        ;
    }
}
