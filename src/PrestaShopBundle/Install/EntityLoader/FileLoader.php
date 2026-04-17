<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Install\EntityLoader;

use PrestashopInstallerException;
use SimpleXMLElement;

/**
 * Loads entity data from an XML file
 */
class FileLoader
{
    public const FALLBACK_LANGUAGE_CODE = 'en';

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

            $path = $this->getFilePath($entity, $iso);

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
     * Looks up the file in a list of lookup paths and returns the first one found.
     *
     * @param string $entity Entity name
     * @param string|null $iso [default=null] 2-letter language code
     *
     * @return string Path to the file
     *
     * @throws PrestashopInstallerException If the file is not found in paths
     */
    private function getFilePath(string $entity, ?string $iso = null): string
    {
        $paths = $this->getLookupPaths($entity, $iso);

        foreach ($paths as $path) {
            if (file_exists($path)) {
                return $path;
            }
        }

        // file not found
        throw new PrestashopInstallerException('XML data file ' . $entity . '.xml not found');
    }

    /**
     * Returns the lookup paths for the provided entity / iso.
     *
     * @param string $entity Entity name
     * @param string|null $iso [default=null] 2-letter language code
     *
     * @return string[] List of lookup paths
     */
    private function getLookupPaths(string $entity, ?string $iso = null): array
    {
        $fileName = "$entity.xml";

        // default path
        if (empty($iso)) {
            return [
                $this->dataPath . $fileName,
            ];
        }

        // preferred path
        $paths = [
            $this->langPath . $iso . '/data/' . $fileName,
        ];

        // add fallback language only if not the same language
        if ($iso !== static::FALLBACK_LANGUAGE_CODE) {
            $paths[] = $this->langPath . self::FALLBACK_LANGUAGE_CODE . '/data/' . $fileName;
        }

        return $paths;
    }
}
