<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Customer\QueryResult;

/**
 * Class DiscountInformation.
 */
class DiscountInformation
{
    /**
     * @var int
     */
    private $discountId;

    /**
     * @var string
     */
    private $code;

    /**
     * @var string
     */
    private $name;

    /**
     * @var bool
     */
    private $isActive;

    /**
     * @var int
     */
    private $availableQuantity;

    /**
     * @param int $discountId
     * @param string $code
     * @param string $name
     * @param bool $isActive
     * @param int $availableQuantity
     */
    public function __construct(
        $discountId,
        $code,
        $name,
        $isActive,
        $availableQuantity
    ) {
        $this->discountId = $discountId;
        $this->code = $code;
        $this->name = $name;
        $this->isActive = $isActive;
        $this->availableQuantity = $availableQuantity;
    }

    /**
     * @return int
     */
    public function getDiscountId()
    {
        return $this->discountId;
    }

    /**
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return bool
     */
    public function isActive()
    {
        return $this->isActive;
    }

    /**
     * @return int
     */
    public function getAvailableQuantity()
    {
        return $this->availableQuantity;
    }
}
