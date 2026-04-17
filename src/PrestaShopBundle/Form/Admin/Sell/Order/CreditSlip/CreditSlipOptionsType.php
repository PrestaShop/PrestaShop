<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Form\Admin\Sell\Order\CreditSlip;

use PrestaShopBundle\Form\Admin\Type\TranslatableType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Backwards compatibility break introduced in 1.7.8.0 due to extension of TranslationAwareType instead of using translator as dependency.
 *
 * Defines credit slips options form
 */
final class CreditSlipOptionsType extends TranslatorAwareType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('slip_prefix', TranslatableType::class, [
            'label' => $this->trans('Credit slip prefix', 'Admin.Orderscustomers.Feature'),
            'help' => $this->trans('Prefix used for credit slips.', 'Admin.Orderscustomers.Help'),
            'required' => false,
            'error_bubbling' => true,
            'type' => TextType::class,
        ]);
    }
}
