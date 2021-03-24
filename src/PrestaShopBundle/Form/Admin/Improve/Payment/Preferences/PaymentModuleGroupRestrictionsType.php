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
 * Class PaymentModuleGroupRestrictionsType defines Group Restriction form in "Improve > Payment > Preferences" page.
 */
class PaymentModuleGroupRestrictionsType extends PaymentModuleRestrictionsParentType
{
    /**
     * @var array
     */
    private $groupChoices;

    /**
     * @param TranslatorInterface $translator
     * @param array $locales
     * @param array $paymentModules
     * @param array $groupChoices
     */
    public function __construct(
        TranslatorInterface $translator,
        array $locales,
        array $paymentModules,
        array $groupChoices
    ) {
        parent::__construct($translator, $locales, $paymentModules);

        $this->groupChoices = $groupChoices;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('group_restrictions', MaterialMultipleChoiceTableType::class, [
                'label' => $this->trans('Group restrictions', 'Admin.Payment.Feature'),
                'help' => $this->trans(
                    'Please select available payment modules for each customer group.',
                    'Admin.Payment.Help'
                ),
                'required' => false,
                'choices' => $this->groupChoices,
                'multiple_choices' => $this->getGroupChoicesForPaymentModules(),
                'headers_fixed' => true,
            ]);
    }

    /**
     * Get multiple group choices for payment modules.
     *
     * @return array<int, array<string, string|bool|array>>
     */
    private function getGroupChoicesForPaymentModules(): array
    {
        $groupChoices = [];

        foreach ($this->paymentModules as $paymentModule) {
            $groupChoices[] = [
                'name' => $paymentModule->get('name'),
                'label' => $paymentModule->get('displayName'),
                'multiple' => true,
                'choices' => $this->groupChoices,
            ];
        }

        return $groupChoices;
    }
}
