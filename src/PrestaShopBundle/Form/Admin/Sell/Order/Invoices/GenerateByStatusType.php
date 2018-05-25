<?php
/**
 * 2007-2018 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShopBundle\Form\Admin\Sell\Order\Invoices;

use PrestaShopBundle\Form\Admin\Type\CommonAbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class generates "By order status" form
 * in "Sell > Orders > Invoices" page.
 */
class GenerateByStatusType extends CommonAbstractType
{
    /**
     * @var array
     */
    private $orderStateChoices;

    /**
     * @var array
     */
    private $orderCountsByState;

    /**
     * @param array $orderStateChoices
     * @param array $orderCountsByState
     */
    public function __construct(array $orderStateChoices, array $orderCountsByState)
    {
        $this->orderStateChoices = $orderStateChoices;
        $this->orderCountsByState = $orderCountsByState;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('order_states', ChoiceType::class, [
                'expanded' => true,
                'multiple' => true,
                'choices' => $this->orderStateChoices,
                'choice_label' => function ($orderStateId, $name) {
                    $orderCount = array_key_exists($orderStateId, $this->orderCountsByState) ?
                        $this->orderCountsByState[$orderStateId] :
                        0
                    ;

                    //@todo improve presentation
                    return $orderCount.' '.$name;
                }
            ])
        ;
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
        return 'orders_invoices_by_status_block';
    }
}
