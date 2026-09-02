<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Support;

use Contact;
use PrestaShopCollection;

/**
 * Class ContactDeleter deletes contact records, using legacy code.
 */
final class ContactDeleter
{
    /**
     * Delete contacts by given IDs.
     *
     * @param array $contactIds
     *
     * @return array of errors
     */
    public function delete(array $contactIds)
    {
        $errors = [];

        if (empty($contactIds)) {
            $errors[] = [
                'key' => 'You must select at least one element to delete.',
                'parameters' => [],
                'domain' => 'Admin.Notifications.Error',
            ];

            return $errors;
        }

        $contactCollection = new PrestaShopCollection(Contact::class);
        $contactCollection->where('id_contact', 'in', $contactIds);

        foreach ($contactCollection as $contact) {
            if (!$contact->delete()) {
                $errors[] = [
                    'key' => 'Can\'t delete #%id%',
                    'parameters' => [
                        '%id%' => $contact->id,
                    ],
                    'domain' => 'Admin.Notifications.Error',
                ];

                continue;
            }
        }

        return $errors;
    }
}
