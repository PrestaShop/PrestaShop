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

use PrestaShop\PrestaShop\Core\Form\FormChoiceProviderInterface;
use PrestaShopBundle\Form\Admin\Type\SwitchType;
use PrestaShopBundle\Form\Admin\Type\TranslateTextType;
use PrestaShopBundle\Form\Admin\Type\TranslateType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
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

    public function __construct(
        TranslatorInterface $translator,
        array $locales,
        FormChoiceProviderInterface $invoiceModelChoiceProvider
    ) {
        parent::__construct($translator, $locales);
        $this->invoiceModelChoiceProvider = $invoiceModelChoiceProvider;
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
            ->add('invoice_prefix', TranslateTextType::class, [
                'locales' => $this->locales,
            ])
            ->add('add_current_year', SwitchType::class)
            ->add('reset_number_annually', SwitchType::class)
            ->add('year_position', ChoiceType::class, [
                'choices'  => [
                    'After the sequential number' => 0,
                    'Before the sequential number' => 1,
                ],
                'expanded' => true,
            ])
            ->add('invoice_number', NumberType::class)
            ->add('legal_free_text', TranslateType::class,
                [
                    'type' => TextareaType::class,
                    'options' => [
                        'required' => false,
                    ],
                    'locales' => $this->locales,
                    'hideTabs' => false,
                ])
            ->add('footer_text', TranslateTextType::class, [
                'locales' => $this->locales,
            ])
            ->add('invoice_model', ChoiceType::class, [
                'choices'  => $this->invoiceModelChoiceProvider->getChoices(),
            ])
            ->add('use_disk_cache', SwitchType::class)
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
}
