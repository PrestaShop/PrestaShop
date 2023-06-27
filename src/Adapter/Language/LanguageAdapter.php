<?php

namespace PrestaShop\PrestaShop\Adapter\Language;

use Language;

class LanguageAdapter
{
    public function getLanguages(bool $active = true, $id_shop = false, bool $ids_only = false): array
    {
        return Language::getLanguages($active, $id_shop, $ids_only);
    }

    public function getInstalledLanguages(): array
    {
        return $this->getLanguages(false);
    }
}
