<?php

namespace PrestaShop\PrestaShop\tests\Fake;

use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use Exception;

class FakeConfiguration implements ConfigurationInterface
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
