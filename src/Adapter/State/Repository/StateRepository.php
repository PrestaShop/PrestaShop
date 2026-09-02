<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\State\Repository;

use PrestaShop\PrestaShop\Core\Domain\AttributeGroup\Attribute\Exception\AttributeNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\State\Exception\StateNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\State\ValueObject\StateId;
use PrestaShop\PrestaShop\Core\Exception\CoreException;
use PrestaShop\PrestaShop\Core\Repository\AbstractMultiShopObjectModelRepository;
use State;

/**
 * Provides access to state data source
 */
class StateRepository extends AbstractMultiShopObjectModelRepository
{
    /**
     * @param StateId $stateId
     *
     * @return State
     *
     * @throws AttributeNotFoundException
     * @throws CoreException
     */
    public function get(StateId $stateId): State
    {
        /** @var State $state */
        $state = $this->getObjectModel(
            $stateId->getValue(),
            State::class,
            StateNotFoundException::class
        );

        return $state;
    }
}
