<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Form\Admin\Sell\CustomerService\MerchandiseReturn;

use PrestaShopBundle\Form\Admin\Type\MultistoreConfigurationType;
use PrestaShopBundle\Form\Admin\Type\SwitchType;
use PrestaShopBundle\Form\Admin\Type\TranslatableType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use PrestaShopBundle\Form\Extension\MultistoreConfigurationTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;

/**
 * Form type for merchandise returns options
 */
class MerchandiseReturnOptionsType extends TranslatorAwareType
{
    public const FIELD_ENABLE_ORDER_RETURN = 'enable_order_return';
    public const FIELD_ORDER_RETURN_PERIOD_IN_DAYS = 'order_return_period_in_days';
    public const FIELD_ORDER_RETURN_PREFIX = 'order_return_prefix';

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(static::FIELD_ENABLE_ORDER_RETURN, SwitchType::class, [
                'label' => $this->trans(
                    'Enable returns',
                    'Admin.Orderscustomers.Feature'
                ),
                'required' => false,
                'multistore_configuration_key' => 'PS_ORDER_RETURN',
            ])
            ->add(static::FIELD_ORDER_RETURN_PERIOD_IN_DAYS, IntegerType::class, [
                'label' => $this->trans(
                    'Return window',
                    'Admin.Orderscustomers.Feature'
                ),
                'help' => $this->trans(
                    'Maximum number of days to return a product after the delivery date.',
                    'Admin.Orderscustomers.Help'
                ),
                'required' => false,
                'constraints' => new GreaterThanOrEqual([
                    'value' => 0,
                ]),
                'multistore_configuration_key' => 'PS_ORDER_RETURN_NB_DAYS',
            ])
            ->add(static::FIELD_ORDER_RETURN_PREFIX, TranslatableType::class, [
                'label' => $this->trans(
                    'Return prefix',
                    'Admin.Orderscustomers.Feature'
                ),
                'help' => $this->trans(
                    'Prefix used for merchandise returns (e.g. RE00001).',
                    'Admin.Orderscustomers.Help'
                ),
                'required' => false,
                'multistore_configuration_key' => 'PS_RETURN_PREFIX',
            ])
        ;
    }

    /**
     * {@inheritdoc}
     *
     * @see MultistoreConfigurationTypeExtension
     */
    public function getParent(): string
    {
        return MultistoreConfigurationType::class;
    }
}
