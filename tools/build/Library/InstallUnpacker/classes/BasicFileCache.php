<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

/**
 * Very basic file cache.
 */
class BasicFileCache
{
    /**
     * @param string $filename
     *
     * @return string
     */
    public function get($filename)
    {
        if (false === $this->isCached($filename)) {
            throw new \Exception(sprintf('No cache entry for %s', $filename));
        }

        $filepath = $this->computeCacheFilepath($filename);

        return file_get_contents($filepath);
    }

    /**
     * @param string $filename
     *
     * @return bool
     */
    public function isCached($filename)
    {
        $filepath = $this->computeCacheFilepath($filename);

        return is_file($filepath) && is_readable($filepath);
    }

    /**
     * @param string $data
     * @param string $filename
     *
     * @return bool
     *
     * @throws Exception
     */
    public function save($data, $filename)
    {
        $filepath = $this->computeCacheFilepath($filename);

        if (is_file($filepath)) {
            throw new \Exception(sprintf('Could not cache file %s', $filepath));
        }

        return (bool) file_put_contents($filepath, $data);
    }

    /**
     * @param string $filename
     *
     * @return bool
     */
    public function delete($filename)
    {
        $filepath = $this->computeCacheFilepath($filename);

        return unlink($filepath);
    }

    /**
     * @param string $filename
     *
     * @return string
     */
    private function computeCacheFilepath($filename)
    {
        return __DIR__ . DIRECTORY_SEPARATOR . $filename . '.cache';
    }
}
