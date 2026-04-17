<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Form\Admin\Sell\Order\Invoices;

use PrestaShopBundle\Form\Admin\Type\DatePickerType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class generates "By Date" form
 * in "Sell > Orders > Invoices" page.
 */
class GenerateByDateType extends TranslatorAwareType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('date_from', DatePickerType::class, [
                'label' => $this->trans('From', 'Admin.Global'),
                'help' => $this->trans('Format: 2011-12-31 (inclusive).', 'Admin.Orderscustomers.Help'),
            ])
            ->add('date_to', DatePickerType::class, [
                'label' => $this->trans('To', 'Admin.Global'),
                'help' => $this->trans('Format: 2011-12-31 (inclusive).', 'Admin.Orderscustomers.Help'),
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'translation_domain' => 'Admin.Orderscustomers.Feature',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'orders_invoices_by_date_block';
    }
}
