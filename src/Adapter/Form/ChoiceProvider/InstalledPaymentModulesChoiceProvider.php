<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Form\ChoiceProvider;

use Module;
use PaymentModule;
use PrestaShop\PrestaShop\Core\Form\FormChoiceProviderInterface;
use Validate;

final class InstalledPaymentModulesChoiceProvider implements FormChoiceProviderInterface
{
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
}
