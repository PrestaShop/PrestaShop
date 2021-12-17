<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace PrestaShopBundle\Form\Admin\Sell\Order;

use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\CleanHtml;
use PrestaShop\PrestaShop\Core\Domain\OrderMessage\OrderMessageConstraint;
use PrestaShop\PrestaShop\Core\Form\ConfigurableFormChoiceProviderInterface;
use PrestaShop\PrestaShop\Core\Form\FormChoiceProviderInterface;
use PrestaShopBundle\Form\Admin\Type\TextWithLengthCounterType;
use PrestaShopBundle\Translation\TranslatorAwareTrait;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

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
     * @var ConfigurableFormChoiceProviderInterface
     */
    private $orderMessageChoiceProvider;

    public function __construct(
        FormChoiceProviderInterface $orderMessageNameChoiceProvider,
        ConfigurableFormChoiceProviderInterface $orderMessageChoiceProvider
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
            ->add('message', TextWithLengthCounterType::class, [
                'input' => 'textarea',
                'max_length' => OrderMessageConstraint::MAX_MESSAGE_LENGTH,
                'position' => 'after',
                'constraints' => [
                    new NotBlank([
                        'message' => $this->trans(
                            'This field cannot be empty.', [], 'Admin.Notifications.Error'
                        ),
                    ]),
                    new CleanHtml([
                        'message' => $this->trans(
                            'The %s field is not valid',
                            [
                                sprintf('"%s"', $this->trans('Message', [], 'Admin.Global')),
                            ],
                            'Admin.Notifications.Error'
                        ),
                    ]
                    ),
                    new Length([
                        'max' => OrderMessageConstraint::MAX_MESSAGE_LENGTH,
                        'maxMessage' => $this->trans(
                            'This field cannot be longer than %limit% characters',
                            ['%limit%' => OrderMessageConstraint::MAX_MESSAGE_LENGTH],
                            'Admin.Notifications.Error'
                        ),
                    ]),
                ],
            ])
        ;
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['messages'] = $this->orderMessageChoiceProvider->getChoices([
            'lang_id' => $options['data']['lang_id'] ?? null,
        ]);
    }
}
