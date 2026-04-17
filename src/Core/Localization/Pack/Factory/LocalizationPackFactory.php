<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Localization\Pack\Factory;

use PrestaShop\PrestaShop\Adapter\Entity\LocalizationPack;

/**
 * Class LocalizationPackFactory is responsible for creating localization pack instances.
 */
final class LocalizationPackFactory implements LocalizationPackFactoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function createNew()
    {
        return new LocalizationPack();
    }
}
