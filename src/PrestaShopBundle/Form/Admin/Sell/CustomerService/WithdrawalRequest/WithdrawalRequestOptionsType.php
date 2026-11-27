<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Form\Admin\Sell\CustomerService\WithdrawalRequest;

use PrestaShop\PrestaShop\Core\Domain\WithdrawalRequest\Service\WithdrawalDeadlineCalculator;
use PrestaShopBundle\Form\Admin\Type\MultistoreConfigurationType;
use PrestaShopBundle\Form\Admin\Type\SwitchType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use PrestaShopBundle\Form\Extension\MultistoreConfigurationTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Form type for the withdrawal function options.
 */
final class WithdrawalRequestOptionsType extends TranslatorAwareType
{
    public const FIELD_ENABLE_WITHDRAWAL_FUNCTION = 'enable_withdrawal_function';
    public const FIELD_WITHDRAWAL_PERIOD_IN_DAYS = 'withdrawal_period_in_days';

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(static::FIELD_ENABLE_WITHDRAWAL_FUNCTION, SwitchType::class, [
                'label' => $this->trans('Enable the withdrawal function', 'Admin.Orderscustomers.Feature'),
                'help' => $this->trans(
                    'Required for consumer sales in the European Union since June 19, 2026 (Directive (EU) 2023/2673): customers can withdraw from an order online, from their account, the order details and the order tracking page. Only disable it if your shop does not sell to consumers.',
                    'Admin.Orderscustomers.Help'
                ),
                'required' => false,
                'multistore_configuration_key' => 'PS_WITHDRAWAL_FUNCTION',
            ])
            ->add(static::FIELD_WITHDRAWAL_PERIOD_IN_DAYS, IntegerType::class, [
                'label' => $this->trans('Withdrawal period', 'Admin.Orderscustomers.Feature'),
                'help' => $this->trans(
                    'Number of days after delivery during which customers can withdraw. The legal minimum is 14 days, you can grant more.',
                    'Admin.Orderscustomers.Help'
                ),
                'unit' => $this->trans('days', 'Admin.Global'),
                'required' => true,
                'constraints' => [
                    new NotBlank(),
                    new GreaterThanOrEqual([
                        'value' => WithdrawalDeadlineCalculator::DEFAULT_PERIOD_DAYS,
                        'message' => $this->trans(
                            'The withdrawal period cannot be shorter than the legal minimum of %days% days.',
                            'Admin.Orderscustomers.Notification',
                            ['%days%' => WithdrawalDeadlineCalculator::DEFAULT_PERIOD_DAYS]
                        ),
                    ]),
                ],
                'multistore_configuration_key' => 'PS_WITHDRAWAL_PERIOD',
            ]);
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
