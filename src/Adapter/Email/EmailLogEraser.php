<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Email;

use PrestaShop\PrestaShop\Adapter\Entity\Mail;
use PrestaShop\PrestaShop\Adapter\Entity\PrestaShopCollection;
use PrestaShop\PrestaShop\Core\Email\EmailLogEraserInterface;

/**
 * Class EmailLogEraser provides API for erasing email logs.
 *
 * @internal
 */
final class EmailLogEraser implements EmailLogEraserInterface
{
    /**
     * {@inheritdoc}
     */
    public function erase(array $mailLogIds): array
    {
        $errors = [];

        if (empty($mailLogIds)) {
            $errors[] = [
                'key' => 'You must select at least one element to delete.',
                'parameters' => [],
                'domain' => 'Admin.Notifications.Error',
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
                    'domain' => 'Admin.Notifications.Error',
                ];

                continue;
            }
        }

        return $errors;
    }

    /**
     * {@inheritdoc}
     */
    public function eraseAll(): bool
    {
        return Mail::eraseAllLogs();
    }
}
