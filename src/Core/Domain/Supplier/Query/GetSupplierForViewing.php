<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Supplier\Query;

use PrestaShop\PrestaShop\Core\Domain\Language\ValueObject\LanguageId;
use PrestaShop\PrestaShop\Core\Domain\Supplier\Exception\SupplierException;
use PrestaShop\PrestaShop\Core\Domain\Supplier\ValueObject\SupplierId;

/**
 * Get supplier information for viewing
 */
class GetSupplierForViewing
{
    /**
     * @var SupplierId
     */
    private $supplierId;

    /**
     * @var LanguageId Language in which supplier is returned
     */
    private $languageId;

    /**
     * @param int $supplierId
     * @param int $languageId
     *
     * @throws SupplierException
     */
    public function __construct($supplierId, $languageId)
    {
        $this->supplierId = new SupplierId($supplierId);
        $this->languageId = new LanguageId($languageId);
    }

    /**
     * @return SupplierId
     */
    public function getSupplierId()
    {
        return $this->supplierId;
    }

    /**
     * @return LanguageId
     */
    public function getLanguageId()
    {
        return $this->languageId;
    }
}
