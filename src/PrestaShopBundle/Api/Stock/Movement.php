<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Api\Stock;

use PrestaShopBundle\Entity\ProductIdentity;

class Movement
{
    /**
     * @var ProductIdentity
     */
    private $productIdentity;

    /**
     * @var int
     */
    private $delta;

    public function __construct(ProductIdentity $productIdentity, $delta)
    {
        $this->productIdentity = $productIdentity;
        $this->delta = (int) $delta;
    }

    /**
     * @return ProductIdentity
     */
    public function getProductIdentity()
    {
        return $this->productIdentity;
    }

    /**
     * @return int
     */
    public function getDelta()
    {
        return $this->delta;
    }
}
