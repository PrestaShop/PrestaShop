<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Controller\Attribute;

use Attribute;

/*
 *  Attribute for controllers.
 *  Allows you to specify if this, or a particular action, requires to be in context all shop.
 *  Related to the multishop feature.
 *  Analysed in ShopContextListener
 */
#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_METHOD)]
class AllShopContext
{
}
