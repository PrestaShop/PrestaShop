<?php
/**
 * 2007-2019 PrestaShop SA and Contributors
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShopBundle\Form\Admin\Sell\Order;

use PrestaShop\PrestaShop\Core\Form\FormChoiceProviderInterface;
use PrestaShopBundle\Translation\TranslatorAwareTrait;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;

/**
 * Helps to render messages block in orders view page.
 */
class OrderMessageType extends AbstractType
{
    use TranslatorAwareTrait;

    /**
     * @var FormChoiceProviderInterface
     */
    private $orderMessageNameChoiceProvider;

    /**
     * @var FormChoiceProviderInterface
     */
    private $orderMessageChoiceProvider;

    public function __construct(
        FormChoiceProviderInterface $orderMessageNameChoiceProvider,
        FormChoiceProviderInterface $orderMessageChoiceProvider
    ) {
        $this->orderMessageNameChoiceProvider = $orderMessageNameChoiceProvider;
        $this->orderMessageChoiceProvider = $orderMessageChoiceProvider;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('order_message', ChoiceType::class, [
                'choices' => $this->orderMessageNameChoiceProvider->getChoices(),
                'required' => false,
            ])
            ->add('is_displayed_to_customer', CheckboxType::class, [
                'required' => false,
                'label' => $this->trans('Display to customer?', [], 'Admin.Orderscustomers.Feature'),
            ])
            ->add('message', TextareaType::class)
        ;
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['messages'] = $this->orderMessageChoiceProvider->getChoices();
    }
}
