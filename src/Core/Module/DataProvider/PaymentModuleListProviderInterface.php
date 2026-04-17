<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Module\DataProvider;

/**
 * Interface PaymentModuleProviderInterface defines contract for payment module list provider.
 */
interface PaymentModuleListProviderInterface
{
    /**
     * Get payment module data.
     *
     * @return array
     */
    public function getPaymentModuleList();
}
