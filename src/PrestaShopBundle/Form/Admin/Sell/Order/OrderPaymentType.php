<?php
/**
 * 2007-2019 PrestaShop and Contributors
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

use PrestaShop\PrestaShop\Core\Form\ConfigurableFormChoiceProviderInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OrderPaymentType extends AbstractType
{
    /**
     * @var ConfigurableFormChoiceProviderInterface
     */
    private $currencySymbolByIdChoiceProvider;

    /**
     * @var ConfigurableFormChoiceProviderInterface
     */
    private $orderInvoiceChoiceProvider;

    /**
     * @var int
     */
    private $contextLanguageId;

    /**
     * @var int
     */
    private $contextShopId;

    /**
     * @param ConfigurableFormChoiceProviderInterface $currencySymbolByIdChoiceProvider
     * @param ConfigurableFormChoiceProviderInterface $orderInvoiceChoiceProvider
     * @param int $contextLanguageId
     * @param int $contextShopId
     */
    public function __construct(
        ConfigurableFormChoiceProviderInterface $currencySymbolByIdChoiceProvider,
        ConfigurableFormChoiceProviderInterface $orderInvoiceChoiceProvider,
        int $contextLanguageId,
        int $contextShopId
    ) {
        $this->currencySymbolByIdChoiceProvider = $currencySymbolByIdChoiceProvider;
        $this->orderInvoiceChoiceProvider = $orderInvoiceChoiceProvider;
        $this->contextLanguageId = $contextLanguageId;
        $this->contextShopId = $contextShopId;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('date', DateType::class, [
                'widget' => 'single_text',
                'input' => 'string',
            ])
            ->add('payment_method', TextType::class)
            ->add('transaction_id', TextType::class, [
                'required' => false,
            ])
            ->add('amount', TextType::class)
            ->add('id_currency', ChoiceType::class, [
                'choices' => $this->currencySymbolByIdChoiceProvider->getChoices([
                    'id_shop' => $this->contextShopId,
                ]),
            ])
            ->add('id_invoice', ChoiceType::class, [
                'choices' => $this->orderInvoiceChoiceProvider->getChoices([
                    'id_order' => $options['id_order'],
                    'id_lang' => $this->contextLanguageId,
                ]),
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setRequired([
                'id_order',
            ])
            ->setAllowedTypes('id_order', 'int')
        ;
    }
}
