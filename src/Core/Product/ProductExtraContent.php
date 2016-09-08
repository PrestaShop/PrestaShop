<?php
/**
 * 2007-2015 PrestaShop
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
 * @copyright 2007-2015 PrestaShop SA
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */
namespace PrestaShop\PrestaShop\Core\Product;

class ProductExtraContent
{
    /**
     * Title of the content. This can be used in the template
     * e.g as a tab name or an anchor.
     * 
     * @var string
     */
    private $title;


    /**
     * Content in HTML to display.
     * This is the main attribute of this class.
     *
     * @var string
     */
    private $content;
    
    /**
     * For some reason, you may need to have a class on the div generated,
     * or to be able to set an anchor.
     * 
     * @var string
     */
    private $attr = '';

    function getTitle() {
        return $this->title;
    }

    function getContent() {
        return $this->content;
    }

    function getAttr() {
        return $this->attr;
    }

    function setTitle($title) {
        $this->title = $title;
        return $this;
    }

    function setContent($content) {
        $this->content = $content;
        return $this;
    }

    function setAttr($attr) {
        $this->attr = $attr;
        return $this;
    }

    
    public function toArray()
    {
        return array(
            'title' => $this->title,
            'content' => $this->content,
            'attr' => $this->attr,
        );
    }

}
