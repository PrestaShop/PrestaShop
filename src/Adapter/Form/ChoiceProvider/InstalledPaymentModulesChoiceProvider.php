<?php
/**
 * 2007-2020 PrestaShop SA and Contributors
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
 * @copyright 2007-2020 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Adapter\Form\ChoiceProvider;

use Module;
use PaymentModule;
use PrestaShop\PrestaShop\Core\Form\FormChoiceAttributeProviderInterface;
use PrestaShop\PrestaShop\Core\Form\FormChoiceProviderInterface;
use PrestaShop\PrestaShop\Core\Order\OrderStateDataProviderInterface;
use Validate;

final class InstalledPaymentModulesChoiceProvider implements FormChoiceProviderInterface, FormChoiceAttributeProviderInterface
{
    /**
     * @var int
     */
    protected $contextLangId;
    /**
     * @var OrderStateDataProviderInterface
     */
    protected $orderStateDataProvider;

    /**
     * InstalledPaymentModulesChoiceProvider constructor.
     * @param OrderStateDataProviderInterface $orderStateDataProvider
     * @param int $contextLangId
     */
    public function __construct(OrderStateDataProviderInterface $orderStateDataProvider, int $contextLangId)
    {
        $this->contextLangId = $contextLangId;
        $this->orderStateDataProvider = $orderStateDataProvider;
    }

    private static $paymentModules;

    /**
     * {@inheritdoc}
     */
    public function getChoices(): array
    {
        if (!self::$paymentModules) {
            self::$paymentModules = [];

            foreach (PaymentModule::getInstalledPaymentModules() as $payment) {
                $module = Module::getInstanceByName($payment['name']);
                if (Validate::isLoadedObject($module) && $module->active) {
                    self::$paymentModules[$module->name] = $module->displayName;
                }
            }
        }

        return self::$paymentModules;
    }

    /**
     * @inheritDoc
     */
    public function getChoicesAttributes()
    {
        $attr = [];
        $orderStates = $this->orderStateDataProvider->getOrderStates($this->contextLangId);
        foreach (array_keys($this->getChoices()) as $moduleName) {
            $attr[$moduleName] = array_reduce($orderStates, function ($carry, $item) use ($moduleName) {
                if (empty($carry) && $item['module_name'] == $moduleName) {
                    return $item['id_order_state'];
                }
                return $carry;
            });
        }
        return $attr;
    }
}
