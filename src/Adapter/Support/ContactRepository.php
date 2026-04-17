<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Support;

use PrestaShop\PrestaShop\Adapter\Entity\Contact;
use PrestaShop\PrestaShop\Core\Support\ContactRepositoryInterface;

/**
 * Class ContactRepository is responsible for retrieving contact data from database.
 *
 * @internal
 */
final class ContactRepository implements ContactRepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function findAllByLangId($langId)
    {
        return Contact::getContacts($langId);
    }
}
