<?php
namespace PrestaShop\PrestaShop\tests\Fake;

use Core_Business_ConfigurationInterface;
use Exception;

class FakeConfiguration implements Core_Business_ConfigurationInterface
{
    private $keys;
    private $persistenceKeys;

    public function __construct(array $keys)
    {
        $this->keys = $keys;
        $this->persistenceKeys = array();
    }

    public function get($key)
    {
        if (!array_key_exists($key, $this->keys)) {
            throw new Exception("Key $key does not exist in the fake configuration.");
        }
        return $this->keys[$key];
    }

    public function persistUserData($key, $value)
    {
        $this->persistenceKeys[$key] = $value;
        return $this;
    }

    public function getPersistedUserData($key)
    {
        if (!isset($this->persistenceKeys[$key])) {
            return null;
        }
        return $this->persistenceKeys[$key];
    }
}
