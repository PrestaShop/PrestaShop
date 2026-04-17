<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Form\Admin\Sell\Order\Delivery;

use DateTime;
use PrestaShopBundle\Form\Admin\Type\DatePickerType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * This form class generates the "Pdf" form in Delivery slips page.
 */
class SlipPdfType extends TranslatorAwareType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $now = (new DateTime())->format('Y-m-d');
        $builder
            ->add(
                'date_from',
                DatePickerType::class,
                [
                    'required' => false,
                    'attr' => ['placeholder' => 'YYYY-MM-DD'],
                    'data' => $now,
                    'empty_data' => $now,
                    'label' => $this->trans('From', 'Admin.Global'),
                    'help' => $this->trans(
                        'Format: %s (inclusive).',
                        'Admin.Orderscustomers.Help',
                        [date('Y-m-d')]
                    ),
                ]
            )
            ->add(
                'date_to',
                DatePickerType::class,
                [
                    'required' => false,
                    'attr' => ['placeholder' => 'YYYY-MM-DD'],
                    'data' => $now,
                    'empty_data' => $now,
                    'label' => $this->trans('To', 'Admin.Global'),
                    'help' => $this->trans(
                        'Format: %s (inclusive).',
                        'Admin.Orderscustomers.Help',
                        [date('Y-m-d')]
                    ),
                ]
            );
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'order_delivery_slip_options';
    }
}
