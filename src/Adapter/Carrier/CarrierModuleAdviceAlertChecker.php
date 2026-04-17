<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Carrier;

use Db;

/**
 * Provides will modules advice alert show in carriers page.
 */
class CarrierModuleAdviceAlertChecker
{
    /**
     * Should this show modules advice alert on carriers page?
     *
     * @return bool
     */
    public function isAlertDisplayed(): bool
    {
        // If there is carriers that id_reference is higher than 2, there is non-default
        // carriers and then don't show advice.
        $sql = 'SELECT COUNT(1) FROM `' . _DB_PREFIX_ . 'carrier` WHERE deleted = 0 AND id_reference > 2';

        return Db::getInstance()->executeS($sql, false)->fetchColumn(0) == 0;
    }
}
