<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Localization\Pack\Factory;

use PrestaShop\PrestaShop\Adapter\Entity\LocalizationPack;

/**
 * Interface LocalizationPackFactoryInterface defines contract for localization pack factory.
 */
interface LocalizationPackFactoryInterface
{
    /**
     * Creates new localization pack.
     *
     * @return LocalizationPack
     */
    public function createNew();
}
