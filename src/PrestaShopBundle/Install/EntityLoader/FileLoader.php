<?php

/**
 * 2007-2020 PrestaShop SA and Contributors
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
 * @copyright 2007-2020 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShopBundle\Install\EntityLoader;

use PrestashopInstallerException;
use SimpleXMLElement;

/**
 * Loads entity data from an XML file
 */
class FileLoader
{
    const FALLBACK_LANGUAGE_CODE = 'en';

    /**
     * @var array[] Loaded data, indexed by entity name and iso code
     */
    private $cache = [];

    /**
     * @var string Path for data files
     */
    private $dataPath;

    /**
     * @var string Path for translated data files
     */
    private $langPath;

    /**
     * @param string $dataPath Path for data files
     * @param string $langPath Path for translated data files
     */
    public function __construct(string $dataPath, string $langPath)
    {
        $this->dataPath = $dataPath;
        $this->langPath = $langPath;
    }

    /**
     * Load an entity XML file.
     *
     * @param string $entity Name of the entity to load (eg. 'tab')
     * @param string|null $iso Language in which to load said entity. If not found, will fall back to default language.
     *
     * @return SimpleXMLElement|null
     *
     * @throws PrestashopInstallerException
     */
    public function load(string $entity, $iso = null): ?SimpleXMLElement
    {
        if (!isset($this->cache[$entity][$iso])) {
            // skip hidden files on macos (see https://github.com/PrestaShop/PrestaShop/commit/dd2d7491b483c223b3fe8c010d093b8e6e82f0e6)
            if (in_array($entity[0], ['.', '_'])) {
                return null;
            }

            $path = $this->dataPath . $entity . '.xml';
            if ($iso) {
                $path = $this->langPath . $this->getFallBackToDefaultEntityLanguage($iso, $entity) . '/data/' . $entity . '.xml';
            }

            if (!file_exists($path)) {
                throw new PrestashopInstallerException('XML data file ' . $entity . '.xml not found');
            }

            $this->cache[$entity][$iso] = @simplexml_load_file($path, 'SimplexmlElement');
            if (!$this->cache[$entity][$iso]) {
                throw new PrestashopInstallerException('XML data file ' . $entity . '.xml invalid');
            }
        }

        return $this->cache[$entity][$iso];
    }

    /**
     * Removes an item from cache
     *
     * @param string $entity Entity name
     * @param string|null $iso [default=null] 2-letter language code. If not provided, it flushes all languages for this entity
     */
    public function flushCache(string $entity, ?string $iso = null)
    {
        if (!empty($iso)) {
            unset($this->cache[$entity][$iso]);
        } else {
            unset($this->cache[$entity]);
        }
    }

    /**
     * @param string $iso
     *
     * @return string Returns the provided language code if a data folder for it exists, or the fallback language code instead
     */
    private function getFallbackToDefaultLanguage(string $iso)
    {
        return file_exists($this->langPath . $iso . '/data/') ? $iso : self::FALLBACK_LANGUAGE_CODE;
    }

    /**
     * Returns the provided language code if an entity file for it exists, or the fallback language code instead
     *
     * @param string $iso
     * @param string $entity
     *
     * @return string
     */
    private function getFallBackToDefaultEntityLanguage($iso, $entity)
    {
        if ($this->getFallbackToDefaultLanguage($iso) === self::FALLBACK_LANGUAGE_CODE) {
            return self::FALLBACK_LANGUAGE_CODE;
        }

        return file_exists($this->langPath . $this->getFallbackToDefaultLanguage($iso) . '/data/' . $entity . '.xml') ? $iso :
            self::FALLBACK_LANGUAGE_CODE;
    }
}
