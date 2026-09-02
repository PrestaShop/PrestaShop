<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Hook\QueryResult;

/**
 * Stores query result for getting manufacturer for viewing
 */
class HookStatus
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var bool
     */
    private $isActive;

    public function __construct(int $id, bool $isActive)
    {
        $this->id = $id;
        $this->isActive = $isActive;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->isActive;
    }
}
