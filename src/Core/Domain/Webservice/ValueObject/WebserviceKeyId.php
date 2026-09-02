<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Webservice\ValueObject;

use PrestaShop\PrestaShop\Core\Domain\Webservice\Exception\WebserviceConstraintException;

/**
 * Encapsulates webservice key id value
 */
class WebserviceKeyId
{
    /**
     * @var int
     */
    private $webserviceKeyId;

    /**
     * @param int $webserviceKeyId
     */
    public function __construct($webserviceKeyId)
    {
        $this->assertWebserviceKeyIdIsIntegerGreaterThanZero($webserviceKeyId);

        $this->webserviceKeyId = $webserviceKeyId;
    }

    /**
     * @return int
     */
    public function getValue()
    {
        return $this->webserviceKeyId;
    }

    /**
     * @param int $webserviceKeyId
     */
    private function assertWebserviceKeyIdIsIntegerGreaterThanZero($webserviceKeyId)
    {
        if (!is_int($webserviceKeyId) || $webserviceKeyId <= 0) {
            throw new WebserviceConstraintException(sprintf('Webservice key id must be integer greater than 0, but %s given', var_export($webserviceKeyId, true)));
        }
    }
}
