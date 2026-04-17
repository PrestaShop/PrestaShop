<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Webservice;

use PrestaShopCollection;
use PrestaShopException;
use WebserviceKey;

/**
 * Class WebserviceKeyEraser is responsible for deleting the records from webservice account table.
 */
final class WebserviceKeyEraser
{
    /**
     * Erase given webservice accounts.
     *
     * @param int[] $webServiceKeyIds
     *
     * @return array<int, array<string, array|string>> - array of errors. If array is empty then erase operation succeeded.
     *
     * @throws PrestaShopException
     */
    public function erase(array $webServiceKeyIds)
    {
        $errors = [];

        if (empty($webServiceKeyIds)) {
            $errors[] = [
                'key' => 'You must select at least one element to delete.',
                'parameters' => [],
                'domain' => 'Admin.Notifications.Error',
            ];

            return $errors;
        }

        $webserviceKeys = new PrestaShopCollection(WebserviceKey::class);
        $webserviceKeys->where('id_webservice_account', 'in', $webServiceKeyIds);

        /** @var WebserviceKey $webserviceKey */
        foreach ($webserviceKeys->getResults() as $webserviceKey) {
            if (!$webserviceKey->delete()) {
                $errors[] = [
                    'key' => 'Can\'t delete #%id%',
                    'parameters' => [
                        '%id%' => $webserviceKey->id,
                    ],
                    'domain' => 'Admin.Notifications.Error',
                ];

                continue;
            }
        }

        return $errors;
    }
}
