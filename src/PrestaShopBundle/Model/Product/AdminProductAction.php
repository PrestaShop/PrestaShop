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
 *  @author 	PrestaShop SA <contact@prestashop.com>
 *  @copyright  2007-2016 PrestaShop SA
 *  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */
namespace PrestaShopBundle\Model\Product;

use PrestaShopBundle\Service\Hook\HookContentClassInterface;

class AdminProductAction implements HookContentClassInterface {
    private $href = '';
    private $onclick = '';
    private $target = '';
    private $icon = '';
    private $label = '';
    
    public function getHref() {
        return $this->href;
    }

    public function getOnclick() {
        return $this->onclick;
    }

    public function getTarget() {
        return $this->target;
    }

    public function getIcon() {
        return $this->icon;
    }

    public function getLabel() {
        return $this->label;
    }

    public function setHref($href) {
        $this->href = $href;
        return $this;
    }

    public function setOnclick($onclick) {
        $this->onclick = $onclick;
        return $this;
    }

    public function setTarget($target) {
        $this->target = $target;
        return $this;
    }

    public function setIcon($icon) {
        $this->icon = $icon;
        return $this;
    }

    public function setLabel($label) {
        $this->label = $label;
        return $this;
    }

    public function toArray()
    {
        return array(
            'href' => $this->href,
            'onclick' => $this->onclick,
            'target' => $this->target,
            'icon' => $this->icon,
            'label' => $this->label,
        );
    }
}
