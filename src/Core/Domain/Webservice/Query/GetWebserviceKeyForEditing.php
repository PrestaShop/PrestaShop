<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Webservice\Query;

use PrestaShop\PrestaShop\Core\Domain\Webservice\ValueObject\WebserviceKeyId;

/**
 * Get webservice key data for editing
 */
class GetWebserviceKeyForEditing
{
    /**
     * @var WebserviceKeyId
     */
    private $webserviceKeyId;

    /**
     * @param int $webserviceKeyId
     */
    public function __construct($webserviceKeyId)
    {
        $this->webserviceKeyId = new WebserviceKeyId($webserviceKeyId);
    }

    /**
     * @return WebserviceKeyId
     */
    public function getWebserviceKeyId()
    {
        return $this->webserviceKeyId;
    }
}
