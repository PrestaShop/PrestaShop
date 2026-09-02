<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\OrderReturnState;

use OrderReturnState;
use PrestaShop\PrestaShop\Core\Order\OrderReturnStateDataProviderInterface;

/**
 * Class OrderReturnStateDataProvider provides OrderReturnState data using legacy code.
 */
final class OrderReturnStateDataProvider implements OrderReturnStateDataProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function getOrderReturnStates($languageId)
    {
        return OrderReturnState::getOrderReturnStates($languageId);
    }
}
