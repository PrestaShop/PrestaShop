<?php
/**
 * 2007-2018 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

/**
 * Very basic file cache
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

        file_put_contents($filepath, $data);

        return true;
    }

    /**
     * @param string $filename
     *
     * @return bool
     */
    public function delete($filename)
    {
        $filepath = $this->computeCacheFilepath($filename);

        unlink($filepath);

        return true;
    }

    /**
     * @param string $filename
     *
     * @return string
     */
    private function computeCacheFilepath($filename)
    {
        $filepath = __DIR__ . DIRECTORY_SEPARATOR . $filename . '.cache';

        return $filepath;
    }
}
