<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Customer\Group\Exception;

use Exception;

class BulkDeleteCustomerGroupException extends GroupException
{
    /** @var int[] */
    private array $groupIds;

    /**
     * @param int[] $groupIds
     */
    public function __construct(array $groupIds, string $message = '', int $code = 0, ?Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);

        $this->groupIds = $groupIds;
    }

    /** @return int[] */
    public function getGroupIds(): array
    {
        return $this->groupIds;
    }
}
