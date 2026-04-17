<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Product;

use Exception;
use PrestaShopBundle\Service\Hook\HookFinder;
use Product;

/**
 * This class gets the extra content to display on the product page
 * from the modules hooked on displayProductExtraContent.
 */
class ProductExtraContentFinder extends HookFinder
{
    protected $hookName = 'displayProductExtraContent';
    protected $expectedInstanceClasses = ['PrestaShop\PrestaShop\Core\Product\ProductExtraContent'];

    /**
     * Execute hook to get all addionnal product content, and check if valid
     * (not empty and only instances of class ProductExtraContent).
     *
     * @return array
     *
     * @throws Exception
     */
    public function find()
    {
        // Check first that we have a product to send as params
        if (!array_key_exists('product', $this->params) || !$this->params['product'] instanceof Product) {
            throw new Exception('Required product param not found.');
        }

        return parent::find();
    }
}
