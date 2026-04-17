<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Translation\Provider;

use Symfony\Component\Translation\MessageCatalogueInterface;

/**
 * Defines what should be the default catalogue, contains all the translations keys.
 */
interface UseDefaultCatalogueInterface
{
    /**
     * Get the default (aka untranslated) catalogue
     *
     * @param bool $empty if true, empty the catalogue values (keep the keys)
     *
     * @return MessageCatalogueInterface Return a default catalogue with all keys
     */
    public function getDefaultCatalogue($empty = true);

    /**
     * @return string Path to the default directory
     *                Most of the time, it's `app/Resources/translations/default/{locale}`
     */
    public function getDefaultResourceDirectory();
}
