<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Behaviour\Features\Context;

use State;

class StateFeatureContext extends AbstractPrestaShopFeatureContext
{
    /**
     * @When I define an uncreated state :stateReference
     *
     * @param string $stateReference
     */
    public function defineUnCreatedState(string $stateReference): void
    {
        SharedStorage::getStorage()->set($stateReference, 0);
    }
}
