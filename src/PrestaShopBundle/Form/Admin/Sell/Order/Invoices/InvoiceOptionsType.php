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

namespace PrestaShopBundle\Form\Admin\Sell\Order\Invoices;

use PrestaShop\PrestaShop\Core\Form\FormChoiceProviderInterface;
use PrestaShopBundle\Form\Admin\Type\SwitchType;
use PrestaShopBundle\Form\Admin\Type\TranslatableType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Translation\TranslatorInterface;

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
            ->add('enable_invoices', SwitchType::class)
            ->add('enable_tax_breakdown', SwitchType::class)
            ->add('enable_product_images', SwitchType::class)
            ->add('invoice_prefix', TranslatableType::class, [
                'type' => TextType::class,
            ])
            ->add('add_current_year', SwitchType::class)
            ->add('reset_number_annually', SwitchType::class)
            ->add('year_position', ChoiceType::class, [
                'choices' => [
                    $this->trans('After the sequential number', 'Admin.Orderscustomers.Feature') => 0,
                    $this->trans('Before the sequential number', 'Admin.Orderscustomers.Feature') => 1,
                ],
                'expanded' => true,
            ])
            ->add('invoice_number', NumberType::class, [
                'required' => false,
            ])
            ->add('legal_free_text', TranslatableType::class, [
                'type' => TextareaType::class,
            ])
            ->add('footer_text', TranslatableType::class, [
                'type' => TextType::class,
            ])
            ->add('invoice_model', ChoiceType::class, [
                'choices' => $this->invoiceModelChoiceProvider->getChoices(),
                'translation_domain' => false,
            ])
            ->add('use_disk_cache', SwitchType::class);
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
}
