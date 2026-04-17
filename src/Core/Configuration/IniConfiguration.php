<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Configuration;

/**
 * Gets ini configuration.
 */
class IniConfiguration
{
    /**
     * Get max post max size from ini configuration in bytes.
     *
     * @return int
     */
    public function getPostMaxSizeInBytes()
    {
        return $this->convertToBytes(ini_get('post_max_size'));
    }

    /**
     * Get maximum upload size allowed by the server in bytes.
     *
     * @return int
     */
    public function getUploadMaxSizeInBytes()
    {
        return min(
            $this->convertToBytes(ini_get('upload_max_filesize')),
            $this->getPostMaxSizeInBytes()
        );
    }

    /**
     * Convert a numeric value to bytes.
     *
     * @param string $value
     *
     * @return int
     */
    private function convertToBytes($value)
    {
        $bytes = (int) trim($value);
        $last = strtolower($value[strlen($value) - 1]);

        switch ($last) {
            case 'g':
                $bytes *= 1024;
                // no break to fall-through
            case 'm':
                $bytes *= 1024;
                // no break to fall-through
            case 'k':
                $bytes *= 1024;
        }

        return $bytes;
    }
}
