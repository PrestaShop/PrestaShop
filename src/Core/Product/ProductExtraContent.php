<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
