<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Form\Admin\Sell\Customer;

use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class TransferGuestAccountType is type used to submit guest customer transformation
 * into actual customer with password.
 */
class TransferGuestAccountType extends TranslatorAwareType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('id_customer', HiddenType::class)
            ->add('transfer_guest_account', SubmitType::class, [
                'attr' => [
                    'class' => 'btn-sm btn-primary',
                ],
                'label' => $this->trans('Transform to a customer account', 'Admin.Orderscustomers.Feature'),
            ]);
    }
}
