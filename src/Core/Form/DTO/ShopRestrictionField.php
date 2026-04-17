<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Form\DTO;

/**
 * Holds the field name for which the multi-store restriction checkbox has been checked or unchecked and the status if it
 * was restricted or not.
 */
class ShopRestrictionField
{
    /**
     * @var string
     */
    private $fieldName;

    /**
     * @var bool
     */
    private $isRestrictedToContextShop;

    /**
     * @param string $fieldName
     * @param bool $isRestrictedToContextShop
     */
    public function __construct($fieldName, $isRestrictedToContextShop)
    {
        $this->fieldName = $fieldName;
        $this->isRestrictedToContextShop = $isRestrictedToContextShop;
    }

    /**
     * @return string
     */
    public function getFieldName()
    {
        return $this->fieldName;
    }

    /**
     * @return bool
     */
    public function isRestrictedToContextShop()
    {
        return $this->isRestrictedToContextShop;
    }
}
