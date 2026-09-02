<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Manufacturer\Query;

use PrestaShop\PrestaShop\Core\Domain\Language\ValueObject\LanguageId;
use PrestaShop\PrestaShop\Core\Domain\Manufacturer\ValueObject\ManufacturerId;

/**
 * Get manufacturer information for viewing
 */
class GetManufacturerForViewing
{
    /**
     * @var ManufacturerId
     */
    private $manufacturerId;

    /**
     * @var LanguageId Language in which manufacturer is returned
     */
    private $languageId;

    /**
     * @param int $manufacturerId
     * @param int $languageId
     */
    public function __construct($manufacturerId, $languageId)
    {
        $this->manufacturerId = new ManufacturerId($manufacturerId);
        $this->languageId = new LanguageId($languageId);
    }

    /**
     * @return ManufacturerId
     */
    public function getManufacturerId()
    {
        return $this->manufacturerId;
    }

    /**
     * @return LanguageId
     */
    public function getLanguageId()
    {
        return $this->languageId;
    }
}
