<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Util\Url;

/**
 * Class UrlFileChecker
 */
final class UrlFileChecker implements UrlFileCheckerInterface
{
    /**
     * @var string
     */
    private $fileDir;

    /**
     * @param string $fileDir
     */
    public function __construct($fileDir)
    {
        $this->fileDir = $fileDir;
    }

    /**
     * @return bool
     */
    public function isHtaccessFileWritable()
    {
        return $this->isFileWritable('.htaccess');
    }

    /**
     * @return bool
     */
    public function isRobotsFileWritable()
    {
        return $this->isFileWritable('robots.txt');
    }

    /**
     * @param string $fileName
     *
     * @return bool
     */
    private function isFileWritable($fileName)
    {
        $filePath = $this->fileDir . DIRECTORY_SEPARATOR . $fileName;

        if (file_exists($filePath)) {
            return is_writable($filePath);
        }

        return is_writable($this->fileDir);
    }
}
