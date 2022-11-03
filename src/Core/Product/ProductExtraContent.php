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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace PrestaShop\PrestaShop\Core\Product;

use PrestaShopBundle\Service\Hook\HookContentClassInterface;

class ProductExtraContent implements HookContentClassInterface
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
     * @var array
     */
    private $attr = [
        'id' => '',
        'class' => '',
    ];

    public function getTitle()
    {
        return $this->title;
    }

    public function getContent()
    {
        return $this->content;
    }

    public function getAttr()
    {
        return $this->attr;
    }

    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    public function addAttr($attr)
    {
        $this->attr = array_merge($this->attr, $attr);

        return $this;
    }

    public function setAttr($attr)
    {
        // We declare default values for if and class which
        // could be mandatory in the template
        $this->attr = array_merge([
            'id' => '',
            'class' => '',
        ], $attr);

        return $this;
    }

    public function toArray()
    {
        return [
            'title' => $this->title,
            'content' => $this->content,
            'attr' => $this->attr,
        ];
    }
}
