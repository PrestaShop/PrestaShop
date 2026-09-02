<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Behaviour\Features\Transform;

use Behat\Behat\Context\Context;
use Tests\Integration\Behaviour\Features\Context\SharedStorage;

/**
 * Shared storage related transformations
 */
class SharedStorageTransformContext implements Context
{
    /**
     * Helps access the latest resource that was put into storage.
     *
     * @Transform /^(it|its)$/
     */
    public function getLatestResource()
    {
        return SharedStorage::getStorage()->getLatestResource();
    }
}
