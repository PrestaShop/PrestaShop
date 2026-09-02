<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace Tests\Integration\Behaviour\Features\Context;

use RuntimeException;

/**
 * Shared storage to use between behat contexts
 */
class SharedStorage
{
    /**
     * @var self|null
     */
    protected static $instance;

    /**
     * @var array
     */
    private $storage = [];

    /**
     * Used for accessing latest resource.
     *
     * @var string|null
     */
    private $latestKey;

    /**
     * @return self
     */
    public static function getStorage()
    {
        if (!isset(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * @param string $key
     *
     * @return mixed
     */
    public function get($key)
    {
        if (!$this->exists($key)) {
            throw new RuntimeException(sprintf('Item with key "%s" does not exist', $key));
        }

        return $this->storage[$key];
    }

    /**
     * @param string $key
     * @param mixed $default
     *
     * @return mixed
     */
    public function getWithDefault($key, $default)
    {
        if (!isset($this->storage[$key])) {
            return $default;
        }

        return $this->storage[$key];
    }

    /**
     * @param string $key
     * @param mixed $resource
     */
    public function set($key, $resource)
    {
        $this->storage[$key] = $resource;
        $this->latestKey = $key;
    }

    /**
     * @param string $key
     *
     * @return bool
     */
    public function exists(string $key): bool
    {
        return isset($this->storage[$key]);
    }

    /**
     * @param string|int $key
     */
    public function clear($key): void
    {
        if ($this->exists($key)) {
            unset($this->storage[$key]);
        }
    }

    /**
     * Clean all previously saved data
     */
    public function clean(): void
    {
        $this->storage = [];
        $this->latestKey = null;
    }

    /**
     * Get the resource that was the latest one to be set into the storage.
     *
     * @return mixed
     */
    public function getLatestResource()
    {
        if (!array_key_exists($this->latestKey, $this->storage)) {
            throw new RuntimeException(sprintf('Latest resource with key "%s" does not exist.', $this->latestKey));
        }

        return $this->storage[$this->latestKey];
    }
}
