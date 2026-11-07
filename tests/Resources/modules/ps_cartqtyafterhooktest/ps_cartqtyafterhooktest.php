<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
use Tests\Integration\Classes\Cart\CartUpdateQuantityAfterHookTest;

class Ps_CartQtyAfterHookTest extends Module
{
    public function __construct()
    {
        $this->name = 'ps_cartqtyafterhooktest';
        $this->version = '1.0.0';
        $this->author = 'PrestaShop';
        $this->need_instance = 0;

        parent::__construct();

        $this->displayName = 'Cart quantity after hook test module';
        $this->description = 'Records actionCartUpdateQuantityAfter dispatches for integration tests.';
    }

    public function install()
    {
        return parent::install() && $this->registerHook('actionCartUpdateQuantityAfter');
    }

    public function hookActionCartUpdateQuantityAfter(array $params)
    {
        CartUpdateQuantityAfterHookTest::$calls[] = [
            'id_product' => isset($params['product']) ? (int) $params['product']->id : null,
            'quantity' => $params['quantity'] ?? null,
            'operator' => $params['operator'] ?? null,
            'product_added_to_cart' => $params['product_added_to_cart'] ?? null,
            'cart_total_quantity' => isset($params['cart']) ? (int) $params['cart']->nbProducts() : null,
        ];
    }
}
