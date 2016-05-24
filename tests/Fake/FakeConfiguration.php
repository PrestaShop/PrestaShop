<?php

namespace PrestaShop\PrestaShop\Tests\Fake;

use Core_Business_ConfigurationInterface;
use Exception;

class FakeConfiguration implements Core_Business_ConfigurationInterface
{
    private $keys;

    public function __construct(array $keys)
    {
        $this->keys = $keys;
    }

    public function get($key)
    {
        if (!array_key_exists($key, $this->keys)) {
            throw new Exception("Key $key does not exist in the fake configuration.");
        }
        return $this->keys[$key];
    }
}
