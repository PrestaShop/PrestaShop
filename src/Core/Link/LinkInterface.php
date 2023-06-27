<?php

namespace PrestaShop\PrestaShop\Core\Link;

interface LinkInterface
{
    public function getAdminLink(string $controller, bool $withToken = true, array $extraParams = []);
}
