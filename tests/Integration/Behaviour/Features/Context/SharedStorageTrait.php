<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Behaviour\Features\Context;

trait SharedStorageTrait
{
    /**
     * @return SharedStorage
     */
    protected function getSharedStorage()
    {
        return SharedStorage::getStorage();
    }
}
