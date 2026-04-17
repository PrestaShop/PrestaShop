<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\State\Command;

use PrestaShop\PrestaShop\Core\Domain\State\ValueObject\StateId;

/**
 * Class ToggleStateStatusCommand is responsible for toggling state status
 */
class ToggleStateStatusCommand
{
    /**
     * @var StateId
     */
    private $stateId;

    /**
     * @param int $stateId
     */
    public function __construct(int $stateId)
    {
        $this->stateId = new StateId($stateId);
    }

    /**
     * @return StateId
     */
    public function getStateId(): StateId
    {
        return $this->stateId;
    }
}
