<?php

namespace PrestaShop\PrestaShop\Core\CustomerService;

interface CustomerThreadStatusProviderInterface
{
    /**
     * @return array<string,string>
     */
    public function getStatuses(): array;
}
