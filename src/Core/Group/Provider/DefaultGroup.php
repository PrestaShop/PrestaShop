<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Group\Provider;

/**
 * Stores information for default group
 */
class DefaultGroup
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
     * @param int $groupId
     * @param string $name
     */
    public function __construct($groupId, $name)
    {
        $this->groupId = $groupId;
        $this->name = $name;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->groupId;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
}
