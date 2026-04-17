<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Form\Admin\Sell\Product\Combination;

use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\TypedRegex;
use PrestaShop\PrestaShop\Core\Domain\Combination\CombinationSettings;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductType;
use PrestaShopBundle\Form\Admin\Sell\Product\Stock\QuantityType;
use PrestaShopBundle\Form\Admin\Sell\Product\Stock\StockOptionsType;
use PrestaShopBundle\Form\Admin\Type\DatePickerType;
use PrestaShopBundle\Form\Admin\Type\TranslatableType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;

class CombinationStockType extends TranslatorAwareType
{
    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('quantities', QuantityType::class, [
                'product_id' => $options['product_id'],
                'product_type' => ProductType::TYPE_COMBINATIONS,
            ])
            ->add('options', StockOptionsType::class)
            ->add('available_date', DatePickerType::class, [
                'label' => $this->trans('Availability date', 'Admin.Catalog.Feature'),
                'required' => false,
                'attr' => [
                    'placeholder' => 'YYYY-MM-DD',
                ],
                'modify_all_shops' => true,
            ])
            ->add('available_now_label', TranslatableType::class, [
                'type' => TextType::class,
                'label' => $this->trans('Label when in stock', 'Admin.Catalog.Feature'),
                'required' => false,
                'options' => [
                    'constraints' => [
                        new TypedRegex(TypedRegex::TYPE_GENERIC_NAME),
                        new Length([
                            'max' => CombinationSettings::MAX_AVAILABLE_NOW_LABEL_LENGTH,
                            'maxMessage' => $this->trans(
                                'This field cannot be longer than %limit% characters.',
                                'Admin.Notifications.Error',
                                ['%limit%' => CombinationSettings::MAX_AVAILABLE_NOW_LABEL_LENGTH]
                            ),
                        ]),
                    ],
                ],
                'modify_all_shops' => true,
                'help' => $this->trans('This will be the displayed availability of the combination, if there is at least 1 in stock. If you don\'t enter anything, the base value set on the product will be used.', 'Admin.Catalog.Help'),
            ])
            ->add('available_later_label', TranslatableType::class, [
                'type' => TextType::class,
                'label' => $this->trans(
                    'Label when out of stock',
                    'Admin.Catalog.Feature'
                ),
                'required' => false,
                'options' => [
                    'constraints' => [
                        new TypedRegex(TypedRegex::TYPE_GENERIC_NAME),
                        new Length([
                            'max' => CombinationSettings::MAX_AVAILABLE_LATER_LABEL_LENGTH,
                            'maxMessage' => $this->trans(
                                'This field cannot be longer than %limit% characters.',
                                'Admin.Notifications.Error',
                                ['%limit%' => CombinationSettings::MAX_AVAILABLE_LATER_LABEL_LENGTH]
                            ),
                        ]),
                    ],
                ],
                'modify_all_shops' => true,
                'help' => $this->trans('This will be the displayed availability of the combination, if it\'s not in stock. If you don\'t enter anything, the base value set on the product will be used.', 'Admin.Catalog.Help'),
            ])
        ;
    }

    /**
     * {@inheritDoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);
        $resolver
            ->setDefaults([
                'label' => false,
            ])
            ->setRequired([
                'product_id',
            ])
            ->setAllowedTypes('product_id', 'int')
        ;
    }
}
