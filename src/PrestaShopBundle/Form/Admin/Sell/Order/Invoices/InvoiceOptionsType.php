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

namespace PrestaShopBundle\Form\Admin\Sell\Order\Invoices;

use PrestaShop\PrestaShop\Core\Form\FormChoiceProviderInterface;
use PrestaShopBundle\Form\Admin\Type\MultistoreConfigurationType;
use PrestaShopBundle\Form\Admin\Type\SwitchType;
use PrestaShopBundle\Form\Admin\Type\TranslatableType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use PrestaShopBundle\Form\Extension\MultistoreConfigurationTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\PositiveOrZero;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class InvoiceOptionsType generates "Invoice options" form
 * in "Sell > Orders > Invoices" page.
 */
class InvoiceOptionsType extends TranslatorAwareType
{
    /**
     * @var FormChoiceProviderInterface
     */
    private $invoiceModelChoiceProvider;

    /**
     * @var int the next available invoice number
     */
    private $nextInvoiceNumber;

    /**
     * @param TranslatorInterface $translator
     * @param array $locales
     * @param FormChoiceProviderInterface $invoiceModelChoiceProvider
     * @param int $nextInvoiceNumber the next available invoice number
     */
    public function __construct(
        TranslatorInterface $translator,
        array $locales,
        FormChoiceProviderInterface $invoiceModelChoiceProvider,
        $nextInvoiceNumber
    ) {
        parent::__construct($translator, $locales);
        $this->invoiceModelChoiceProvider = $invoiceModelChoiceProvider;
        $this->nextInvoiceNumber = $nextInvoiceNumber;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'enable_invoices',
                SwitchType::class,
                [
                    'required' => true,
                    'multistore_configuration_key' => 'PS_INVOICE',
                    'label' => $this->trans('Enable invoices', 'Admin.Orderscustomers.Feature'),
                    'help' => $this->trans(
                        'When enabled, your customers will receive an invoice for the purchase.',
                        'Admin.Orderscustomers.Help'
                    ),
                ]
            )
            ->add(
                'enable_tax_breakdown',
                SwitchType::class,
                [
                    'multistore_configuration_key' => 'PS_INVOICE_TAXES_BREAKDOWN',
                    'label' => $this->trans('Enable tax breakdown', 'Admin.Orderscustomers.Feature'),
                    'help' => $this->trans(
                        'If required, show the total amount per rate of the corresponding tax.',
                        'Admin.Orderscustomers.Help'
                    ),
                ]
            )
            ->add(
                'enable_product_images',
                SwitchType::class,
                [
                    'multistore_configuration_key' => 'PS_PDF_IMG_INVOICE',
                    'label' => $this->trans('Enable product image', 'Admin.Orderscustomers.Feature'),
                    'help' => $this->trans(
                        'Adds an image in front of the product name on the invoice.',
                        'Admin.Orderscustomers.Help'
                    ),
                ]
            )
            ->add(
                'invoice_prefix',
                TranslatableType::class,
                [
                    'type' => TextType::class,
                    'multistore_configuration_key' => 'PS_INVOICE_PREFIX',
                    'label' => $this->trans('Invoice prefix', 'Admin.Orderscustomers.Feature'),
                    'help' => $this->trans(
                        'Freely definable prefix for invoice number (e.g. #IN00001).',
                        'Admin.Orderscustomers.Help'
                    ),
                ]
            )
            ->add(
                'add_current_year',
                SwitchType::class,
                [
                    'multistore_configuration_key' => 'PS_INVOICE_USE_YEAR',
                    'label' => $this->trans(
                        'Add current year to invoice number',
                        'Admin.Orderscustomers.Feature'
                    ),
                ]
            )
            ->add(
                'reset_number_annually',
                SwitchType::class,
                [
                    'multistore_configuration_key' => 'PS_INVOICE_RESET',
                    'label' => $this->trans(
                        'Reset sequential invoice number at the beginning of the year',
                        'Admin.Orderscustomers.Feature'
                    ),
                ]
            )
            ->add(
                'year_position',
                ChoiceType::class,
                [
                    'choices' => [
                        $this->trans('After the sequential number', 'Admin.Orderscustomers.Feature') => 0,
                        $this->trans('Before the sequential number', 'Admin.Orderscustomers.Feature') => 1,
                    ],
                    'expanded' => true,
                    'multistore_configuration_key' => 'PS_INVOICE_YEAR_POS',
                    'label' => $this->trans('Position of the year date', 'Admin.Orderscustomers.Feature'),
                ]
            )
            ->add(
                'invoice_number',
                IntegerType::class,
                [
                    'required' => false,
                    'constraints' => [new PositiveOrZero()],
                    'multistore_configuration_key' => 'PS_INVOICE_START_NUMBER',
                    'label' => $this->trans('Invoice number', 'Admin.Orderscustomers.Feature'),
                    'help' => $this->trans(
                        'The next invoice will begin with this number, and then increase with each additional invoice. Set to 0 if you want to keep the current number (which is #%number%).',
                        'Admin.Orderscustomers.Help',
                        [
                            '%number%' => $this->nextInvoiceNumber,
                        ]
                    ),
                ]
            )
            ->add(
                'legal_free_text',
                TranslatableType::class,
                [
                    'type' => TextareaType::class,
                    'multistore_configuration_key' => 'PS_INVOICE_LEGAL_FREE_TEXT',
                    'label' => $this->trans('Legal free text', 'Admin.Orderscustomers.Feature'),
                    'help' => $this->trans(
                        'Use this field to show additional information on the invoice such as specific legal information. It will be displayed below the payment methods summary.',
                        'Admin.Orderscustomers.Help'
                    ),
                ]
            )
            ->add(
                'footer_text',
                TranslatableType::class,
                [
                    'type' => TextType::class,
                    'multistore_configuration_key' => 'PS_INVOICE_FREE_TEXT',
                    'label' => $this->trans('Footer text', 'Admin.Orderscustomers.Feature'),
                    'help' => $this->trans(
                        'This text will appear at the bottom of the invoice, below your company details.',
                        'Admin.Orderscustomers.Help'
                    ),
                ]
            )
            ->add(
                'invoice_model',
                ChoiceType::class,
                [
                    'choices' => $this->invoiceModelChoiceProvider->getChoices(),
                    'translation_domain' => false,
                    'multistore_configuration_key' => 'PS_INVOICE_MODEL',
                    'label' => $this->trans('Invoice model', 'Admin.Orderscustomers.Feature'),
                    'help' => $this->trans('Choose an invoice model', 'Admin.Orderscustomers.Help'),
                ]
            )
            ->add(
                'use_disk_cache',
                SwitchType::class,
                [
                    'multistore_configuration_key' => 'PS_PDF_USE_CACHE',
                    'label' => $this->trans(
                        'Use the disk as cache for PDF invoices',
                        'Admin.Orderscustomers.Feature'
                    ),
                    'help' => $this->trans(
                        'Saves memory but slows down the PDF generation.',
                        'Admin.Orderscustomers.Help'
                    ),
                ]
            );
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['next_invoice_number'] = $this->nextInvoiceNumber;
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
     *
     * @see MultistoreConfigurationTypeExtension
     */
    public function getParent(): string
    {
        return MultistoreConfigurationType::class;
    }
}
