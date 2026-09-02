<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Webservice\Command;

use PrestaShop\PrestaShop\Core\Domain\Webservice\ValueObject\WebserviceKeyId;

/**
 * Deletes state
 */
class DeleteWebserviceKeyCommand
{
    /**
     * @var WebserviceKeyId
     */
    private $webserviceKeyId;

    /**
     * @param int $webserviceKeyId
     */
    public function __construct(int $webserviceKeyId)
    {
        $this->webserviceKeyId = new WebserviceKeyId($webserviceKeyId);
    }

    /**
     * @return WebserviceKeyId
     */
    public function getWebserviceKeyId(): WebserviceKeyId
    {
        return $this->webserviceKeyId;
    }
}
