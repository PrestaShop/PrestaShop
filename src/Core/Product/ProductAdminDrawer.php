<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
