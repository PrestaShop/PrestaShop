<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Webservice\Command;

use PrestaShop\PrestaShop\Core\Domain\Webservice\ValueObject\WebserviceKeyId;

/**
 * Deletes states on bulk action
 */
class BulkDeleteWebserviceKeyCommand
{
    /**
     * @var array<int, WebserviceKeyId>
     */
    private $webserviceKeyIds;

    /**
     * @param array<int, int> $webserviceKeyIds
     */
    public function __construct(array $webserviceKeyIds)
    {
        $this->setWebserviceKeyIds($webserviceKeyIds);
    }

    /**
     * @return array<int, WebserviceKeyId>
     */
    public function getWebserviceKeyIds(): array
    {
        return $this->webserviceKeyIds;
    }

    /**
     * @param array<int, int> $webserviceKeyIds
     */
    private function setWebserviceKeyIds(array $webserviceKeyIds): void
    {
        foreach ($webserviceKeyIds as $webserviceKeyId) {
            $this->webserviceKeyIds[] = new WebserviceKeyId((int) $webserviceKeyId);
        }
    }
}
