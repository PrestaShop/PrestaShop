<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\State\Command;

use PrestaShop\PrestaShop\Core\Domain\State\ValueObject\NoStateId;
use PrestaShop\PrestaShop\Core\Domain\State\ValueObject\StateId;
use PrestaShop\PrestaShop\Core\Domain\State\ValueObject\StateIdInterface;

/**
 * Deletes state
 */
class DeleteStateCommand
{
    /**
     * @var StateIdInterface
     */
    private $stateId;

    /**
     * @param int $stateId
     */
    public function __construct(int $stateId)
    {
        $this->stateId = $stateId !== NoStateId::NO_STATE_ID_VALUE ? new StateId($stateId) : new NoStateId();
    }

    /**
     * @return StateIdInterface
     */
    public function getStateId(): StateIdInterface
    {
        return $this->stateId;
    }
}
