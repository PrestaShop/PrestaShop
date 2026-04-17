<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Translation\Provider;

use Symfony\Component\Translation\MessageCatalogue;

/**
 * Provide an Message Catalogue from the Database
 */
interface DatabaseCatalogueInterface
{
    /**
     * @param string|null $themeName the Theme name
     *
     * @return MessageCatalogue
     */
    public function getDatabaseCatalogue($themeName = null);
}
