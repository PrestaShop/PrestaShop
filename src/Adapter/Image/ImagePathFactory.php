<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Image;

class ImagePathFactory
{
    /**
     * @var string
     */
    private $pathToBaseDir;

    /**
     * @param string $pathToBaseDir
     */
    public function __construct(
        string $pathToBaseDir
    ) {
        $this->pathToBaseDir = rtrim($pathToBaseDir, '/');
    }

    /**
     * @param int|string $imageName
     *
     * @return string
     */
    public function getPath($imageName): string
    {
        return sprintf('%s/%s.jpg', $this->pathToBaseDir, $imageName);
    }
}
