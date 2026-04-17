<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Addon;

class AddonListFilter
{
    /**
     * @var int AddonListFilterType Specify the addon type like theme only or module only or all
     */
    public $type = AddonListFilterType::ALL;

    /**
     * @var int AddonListFilterStatus Specify if you want enabled only, disabled only or all addons
     */
    public $status = AddonListFilterStatus::ALL;

    /**
     * @var int AddonListFilterOrigin Specify if you want an addon from a specific source
     */
    public $origin = AddonListFilterOrigin::ALL;

    /**
     * @var array Names of all the addons to exclude from result
     */
    public $exclude = [];

    /**
     * @param int $origin
     *
     * @return AddonListFilter
     */
    public function addOrigin($origin)
    {
        $this->origin &= $origin;

        return $this;
    }

    /**
     * @param int $status
     *
     * @return AddonListFilter
     */
    public function addStatus($status)
    {
        $this->status &= $status;

        return $this;
    }

    /**
     * @param int $type
     *
     * @return AddonListFilter
     */
    public function addType($type)
    {
        $this->type &= $type;

        return $this;
    }

    /**
     * @param int $origin
     *
     * @return bool
     */
    public function hasOrigin($origin)
    {
        return (bool) ($this->origin & $origin);
    }

    /**
     * @param int $status
     *
     * @return bool
     */
    public function hasStatus($status)
    {
        return (bool) ($this->status & $status);
    }

    /**
     * @param int $type
     *
     * @return bool
     */
    public function hasType($type)
    {
        return (bool) ($this->type & $type);
    }

    /**
     * @param int $origin
     *
     * @return AddonListFilter
     */
    public function removeOrigin($origin)
    {
        return $this->addOrigin(~$origin);
    }

    /**
     * @param int $status
     *
     * @return AddonListFilter
     */
    public function removeStatus($status)
    {
        return $this->addStatus(~$status);
    }

    /**
     * @param int $type
     *
     * @return AddonListFilter
     */
    public function removeType($type)
    {
        return $this->addType(~$type);
    }

    /**
     * @param int $origin
     *
     * @return AddonListFilter
     */
    public function setOrigin($origin)
    {
        $this->origin = $origin;

        return $this;
    }

    /**
     * @param int $type
     *
     * @return AddonListFilter
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @param int $status
     *
     * @return AddonListFilter
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    public function setExclude(array $exclude)
    {
        $this->exclude = $exclude;

        return $this;
    }
}
