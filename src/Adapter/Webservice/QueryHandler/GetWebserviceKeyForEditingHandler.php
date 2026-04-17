<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Webservice\QueryHandler;

use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsQueryHandler;
use PrestaShop\PrestaShop\Core\Domain\Webservice\Exception\WebserviceKeyNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Webservice\Query\GetWebserviceKeyForEditing;
use PrestaShop\PrestaShop\Core\Domain\Webservice\QueryHandler\GetWebserviceKeyForEditingHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Webservice\QueryResult\EditableWebserviceKey;
use PrestaShop\PrestaShop\Core\Domain\Webservice\ValueObject\WebserviceKeyId;
use WebserviceKey;

/**
 * Handles command that gets webservice key data for editing
 *
 * @internal
 */
#[AsQueryHandler]
final class GetWebserviceKeyForEditingHandler implements GetWebserviceKeyForEditingHandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public function handle(GetWebserviceKeyForEditing $query)
    {
        $webserviceKey = $this->getLegacyWebserviceKeyObject($query->getWebserviceKeyId());

        return new EditableWebserviceKey(
            $query->getWebserviceKeyId(),
            $webserviceKey->key,
            $webserviceKey->description,
            $webserviceKey->active,
            WebserviceKey::getPermissionForAccount($webserviceKey->key),
            $webserviceKey->getAssociatedShops()
        );
    }

    /**
     * @param WebserviceKeyId $webserviceKeyId
     *
     * @return WebserviceKey
     */
    private function getLegacyWebserviceKeyObject(WebserviceKeyId $webserviceKeyId)
    {
        $webserviceKey = new WebserviceKey($webserviceKeyId->getValue());

        if ($webserviceKey->id !== $webserviceKeyId->getValue()) {
            throw new WebserviceKeyNotFoundException(
                sprintf('Webservice key with id "%d" was not found', $webserviceKeyId->getValue())
            );
        }

        return $webserviceKey;
    }
}
