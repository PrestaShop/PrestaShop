<?php

class Adapter_Configuration implements Core_Business_Configuration
{
    public function get($key)
    {
        if (defined($key)) {
            return constant($key);
        } else {
            return Configuration::get($key);
        }
    }
}
