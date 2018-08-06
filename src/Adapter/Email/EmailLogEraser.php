<?php
/**
 * 2007-2018 PrestaShop
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

namespace PrestaShop\PrestaShop\Adapter\Email;

use PrestaShop\PrestaShop\Adapter\Entity\Mail;
use PrestaShop\PrestaShop\Adapter\Entity\PrestaShopCollection;
use PrestaShop\PrestaShop\Core\Email\EmailLogEraserInterface;

/**
 * Class EmailLogEraser provides API for erasing email logs
 *
 * @internal
 */
final class EmailLogEraser implements EmailLogEraserInterface
{
    /**
     * {@inheritdoc}
     */
    public function erase(array $mailLogIds)
    {
        $errors = [];

        if (empty($mailLogIds)) {
            $errors[] = [
                'key' => 'You must select at least one element to delete.',
                'parameters' => [],
                'domain' => 'Admin.Notifications.Error'
            ];

            return $errors;
        }

        $emailLogs = new PrestaShopCollection('Mail');
        $emailLogs->where('id_mail', 'in', $mailLogIds);

        /** @var Mail $emailLog */
        foreach ($emailLogs->getResults() as $emailLog) {
            if (!$emailLog->delete()) {
                $errors[] = [
                    'key' => 'Can\'t delete #%id%',
                    'parameters' => [
                        '%id%' => $emailLog->id,
                    ],
                    'domain' => 'Admin.Notifications.Error'
                ];

                continue;
            }
        }

        return $errors;
    }

    /**
     * {@inheritdoc}
     */
    public function eraseAll()
    {
        return Mail::eraseAllLogs();
    }
}
