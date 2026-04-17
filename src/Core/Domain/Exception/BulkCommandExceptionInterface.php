<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Exception;

use Throwable;

interface BulkCommandExceptionInterface extends Throwable
{
    /**
     * @return Throwable[]
     */
    public function getExceptions(): array;
}
