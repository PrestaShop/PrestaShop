<?php
/**
 * 2007-2018 PrestaShop.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Adapter\Webservice;

use PrestaShopCollection;
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
     * @return string[] - array of errors. If array is empty then erase operation succeeded.
     *
     * @throws \PrestaShopException
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
