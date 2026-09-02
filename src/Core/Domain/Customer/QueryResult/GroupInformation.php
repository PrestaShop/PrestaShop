<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Customer\QueryResult;

/**
 * Class GroupInformation holds customer group information.
 */
class GroupInformation
{
    /**
     * @var int
     */
    private $groupId;

    /**
     * @var string
     */
    private $name;

    /**
     * @var bool
     */
    private $isDefault;

    /**
     * @param int $groupId
     * @param string $name
     * @param bool $isDefault
     */
    public function __construct(
        int $groupId,
        string $name,
        bool $isDefault = false
    ) {
        $this->groupId = $groupId;
        $this->name = $name;
        $this->isDefault = $isDefault;
    }

    /**
     * @return int
     */
    public function getGroupId(): int
    {
        return $this->groupId;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return bool
     */
    public function isDefault(): bool
    {
        return $this->isDefault;
    }
}
