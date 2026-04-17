<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Presenter\Module;

use PrestaShop\PrestaShop\Adapter\Module\PaymentModuleListProvider;
use PrestaShop\PrestaShop\Adapter\Presenter\PresenterInterface;

/**
 * Class PaymentModulesPresenter is responsible for presenting payment modules.
 */
class PaymentModulesPresenter
{
    /**
     * @var PresenterInterface
     */
    private $modulePresenter;

    /**
     * @var PaymentModuleListProvider
     */
    private $paymentModuleListProvider;

    /**
     * @param PresenterInterface $modulePresenter
     * @param PaymentModuleListProvider $paymentModuleListProvider
     */
    public function __construct(
        PresenterInterface $modulePresenter,
        PaymentModuleListProvider $paymentModuleListProvider
    ) {
        $this->modulePresenter = $modulePresenter;
        $this->paymentModuleListProvider = $paymentModuleListProvider;
    }

    /**
     * Get presented payment modules.
     *
     * @return array
     */
    public function present(): array
    {
        $installedModules = $this->paymentModuleListProvider->getPaymentModuleList();

        $paymentModulesToDisplay = [];
        foreach ($installedModules as $module) {
            $paymentModulesToDisplay[] = $this->modulePresenter->present($module);
        }

        return $paymentModulesToDisplay;
    }
}
