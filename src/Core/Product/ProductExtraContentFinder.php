<?php
/**
 * 2007-2016 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2016 PrestaShop SA
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */
namespace PrestaShop\PrestaShop\Core\Product;

/**
 * This class gets the extra content to display on the product page
 * from the modules hooked on productExtraContent
 */
class ProductExtraContentFinder {
    /**
     * Execute hook to get all addionnal product content, and check if valid
     * (not empty and only instances of class ProductExtraContent).
     * 
     * @param \Product $product
     * @return array
     * @throws \Exception
     */
    public function getProductExtraContent(\Product $product)
    {
        $extraContent = \Hook::exec('displayProductExtraContent', array('product' => $product), null, true);
        if (!is_array($extraContent)) {
            $extraContent = array();
        }
        foreach ($extraContent as $moduleName => $moduleExtraContents) {
            foreach ($moduleExtraContents as $content) {
                if (!$content instanceof ProductExtraContent) {
                    throw new \Exception('The module '.$moduleName.' did not return expected ProductExtraContent object.');
                }
            }
        }
        return $extraContent;
    }
    
    /**
     * Present all product extra content for templates
     * @param \Product $product
     * @return array
     */
    public function getPresentedProductExtraContent(\Product $product)
    {
        $extraContent = $this->getProductExtraContent($product);
        $presentedExtraContent = array();
        
        foreach ($extraContent as $moduleExtraContents) {
            foreach ($moduleExtraContents as $content) {
                $presentedExtraContent[] = $content->toArray();
            }
        }
        
        return $presentedExtraContent;
    }
}
