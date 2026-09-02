<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Group\Provider;

/**
 * Stores default group options
 */
class DefaultGroups
{
    /**
     * @var DefaultGroup
     */
    private $visitorsGroup;

    /**
     * @var DefaultGroup
     */
    private $guestsGroup;

    /**
     * @var DefaultGroup
     */
    private $customersGroup;

    /**
     * @param DefaultGroup $visitorsGroup
     * @param DefaultGroup $guestsGroup
     * @param DefaultGroup $customersGroup
     */
    public function __construct(DefaultGroup $visitorsGroup, DefaultGroup $guestsGroup, DefaultGroup $customersGroup)
    {
        $this->visitorsGroup = $visitorsGroup;
        $this->guestsGroup = $guestsGroup;
        $this->customersGroup = $customersGroup;
    }

    /**
     * Get default visitors group
     *
     * @return DefaultGroup
     */
    public function getVisitorsGroup()
    {
        return $this->visitorsGroup;
    }

    /**
     * Get default guests group
     *
     * @return DefaultGroup
     */
    public function getGuestsGroup()
    {
        return $this->guestsGroup;
    }

    /**
     * Get customers group
     *
     * @return DefaultGroup
     */
    public function getCustomersGroup()
    {
        return $this->customersGroup;
    }

    /**
     * Get default groups
     *
     * @return DefaultGroup[]
     */
    public function getGroups()
    {
        return [
            $this->getVisitorsGroup(),
            $this->getGuestsGroup(),
            $this->getCustomersGroup(),
        ];
    }
}
