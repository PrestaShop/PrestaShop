<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\File;

use Tools;

/**
 * Class RobotsTextFileGenerator is responsible for generating robots txt file.
 */
class RobotsTextFileGenerator
{
    /**
     * Generates the robots.txt file.
     *
     * @return bool
     */
    public function generateFile()
    {
        return Tools::generateRobotsFile(true);
    }
}
