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

class ProductAdminDrawer implements HookContentClassInterface
{
    /**
     * Material icon reference to display above the title.
     *
     * @var string
     */
    protected $icon;

    /**
     * ID suffix to add in the generated DOM element.
     *
     * @var string
     */
    protected $id;

    /**
     * Destination of the link.
     *
     * @var string
     */
    protected $link;

    /**
     * Title of the button. Should be short.
     *
     * @var string
     */
    protected $title;

    public function __construct(array $data = [])
    {
        if (!empty($data['icon'])) {
            $this->setIcon($data['icon']);
        }
        if (!empty($data['id'])) {
            $this->setId($data['id']);
        }
        if (!empty($data['link'])) {
            $this->setLink($data['link']);
        }
        if (!empty($data['title'])) {
            $this->setTitle($data['title']);
        }
    }

    public function getIcon()
    {
        return $this->icon;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getLink()
    {
        return $this->link;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function setIcon($icon)
    {
        $this->icon = $icon;

        return $this;
    }

    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    public function setLink($link)
    {
        $this->link = $link;

        return $this;
    }

    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    public function toArray()
    {
        return [
            'icon' => $this->icon,
            'id' => $this->id,
            'link' => $this->link,
            'title' => $this->title,
        ];
    }
}
