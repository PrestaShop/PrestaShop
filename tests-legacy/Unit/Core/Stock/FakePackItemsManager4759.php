<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace LegacyTests\Unit\Core\Stock;

class FakePackItemsManager4759
{
    private $packs = array();
    private $items = array();
    private $stockAvailables = array();

    public function addProduct(FakeProduct4759 $pack, FakeProduct4759 $product, $product_attribute_id, $quantity)
    {
        $entry = array(
            'productObj' => $product,
            'id' => $product->id,
            'id_pack_product_attribute' => $product_attribute_id,
            'pack_quantity' => $quantity,
        );
        $this->packs[$pack->id][] = (object) $entry;
        $entry = array(
            'packObj' => $pack,
            'id' => $pack->id,
            'pack_item_quantity' => $quantity,
            'pack_stock_type' => $pack->pack_stock_type,

        );
        $this->items[$product->id][$product_attribute_id][$pack->id] = (object) $entry;
        $this->stockAvailables[$pack->id][0] = $pack->stock_available;
        $this->stockAvailables[$product->id][$product_attribute_id] = $product->stock_available;
    }

    public function getPackItems($pack, $id_lang = false)
    {
        return $this->packs[$pack->id];
    }

    public function getPacksContainingItem($item, $item_attribute_id, $id_lang = false)
    {
        return $this->items[$item->id][$item_attribute_id];
    }

    public function getStockAvailableByProduct($product, $id_product_attribute = null, $id_shop = null)
    {
        $id_product_attribute = $id_product_attribute?$id_product_attribute:0;

        return $this->stockAvailables[$product->id][$id_product_attribute];
    }

    public function isPack($product)
    {
        return isset($this->packs[$product->id]);
    }

    public function isPacked($product, $id_product_attribute = false)
    {
        return isset($this->items[$product->id][$id_product_attribute]);
    }
}
