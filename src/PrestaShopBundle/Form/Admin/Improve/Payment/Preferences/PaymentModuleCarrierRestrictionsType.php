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

declare(strict_types=1);

namespace PrestaShopBundle\Form\Admin\Improve\Payment\Preferences;

use PrestaShopBundle\Form\Admin\Type\Material\MaterialMultipleChoiceTableType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class PaymentModuleCarrierRestrictionsType defines Carrier Restriction form in "Improve > Payment > Preferences" page.
 */
class PaymentModuleCarrierRestrictionsType extends PaymentModuleRestrictionsParentType
{
    /**
     * @var array
     */
    private $carrierChoices;

    /**
     * @param TranslatorInterface $translator
     * @param array $locales
     * @param array $paymentModules
     * @param array $carrierChoices
     */
    public function __construct(
        TranslatorInterface $translator,
        array $locales,
        array $paymentModules,
        array $carrierChoices
    ) {
        parent::__construct($translator, $locales, $paymentModules);

        $this->carrierChoices = $carrierChoices;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('carrier_restrictions', MaterialMultipleChoiceTableType::class, [
                'label' => $this->trans('Carrier restrictions', 'Admin.Payment.Feature'),
                'help' => $this->trans(
                    'Please select available payment modules for each carrier.',
                    'Admin.Payment.Help'
                ),
                'required' => false,
                'choices' => $this->carrierChoices,
                'multiple_choices' => $this->getCarrierChoicesForPaymentModules(),
                'headers_fixed' => true,
            ]);
    }

    /**
     * Get multiple carrier choices for payment modules.
     *
     * @return array
     */
    private function getCarrierChoicesForPaymentModules(): array
    {
        $carrierChoices = [];

        foreach ($this->paymentModules as $paymentModule) {
            $carrierChoices[] = [
                'name' => $paymentModule->get('name'),
                'label' => $paymentModule->get('displayName'),
                'multiple' => true,
                'choices' => $this->carrierChoices,
            ];
        }

        return $carrierChoices;
    }
}
