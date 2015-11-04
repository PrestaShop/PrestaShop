<?php

namespace PrestaShop\PrestaShop\Adapter\Meta;
use PrestaShop\PrestaShop\Core\Foundation\Database\AutoPrefixingDatabase;
use Context;

class MetaDataProvider
{
    private $db;

    public function __construct(AutoPrefixingDatabase $db)
    {
        $this->db = $db;
    }

    public function all(Context $context)
    {
        return $this->db->select(
            'SELECT m.page, ml.title, ml.description
                FROM prefix_meta m
                    INNER JOIN prefix_meta_lang ml
                        ON m.id_meta = ml.id_meta
                WHERE ml.id_shop = :id_shop
                    AND ml.id_lang = :id_lang
                ORDER BY ml.title ASC
                ', [
                'id_shop' => $context->shop->id,
                'id_lang' => $context->language->id
        ]);
    }
}
