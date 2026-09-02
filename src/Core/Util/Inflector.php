<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Util;

use Doctrine\Inflector\Inflector as DoctrineInflector;
use Doctrine\Inflector\InflectorFactory;

/**
 * Utility class to get an Inflector as singleton
 */
class Inflector
{
    private static $inflector;

    public static function getInflector(): DoctrineInflector
    {
        if (null === self::$inflector) {
            self::$inflector = InflectorFactory::create()->build();
        }

        return self::$inflector;
    }
}
