<?php

namespace PrestaShop\PrestaShop\Core\Security;

class Hashing
{
    public function hash(string $passwd, string $salt): string
    {
        return md5($salt . $passwd);
    }
}
