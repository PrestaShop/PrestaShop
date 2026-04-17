<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\MailTemplate;

use PrestaShop\PrestaShop\Core\MailTemplate\Layout\LayoutCollection;
use PrestaShop\PrestaShop\Core\MailTemplate\Layout\LayoutCollectionInterface;

/**
 * Class MailTheme basic immutable implementation of MailThemeInterface.
 */
class Theme implements ThemeInterface
{
    /** @var string */
    private $name;

    /**
     * @var LayoutCollectionInterface
     */
    private $layouts;

    /**
     * @param string $name
     */
    public function __construct($name)
    {
        $this->name = $name;
        $this->layouts = new LayoutCollection();
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return LayoutCollectionInterface
     */
    public function getLayouts()
    {
        return $this->layouts;
    }

    /**
     * @param LayoutCollectionInterface $layouts
     *
     * @return $this
     */
    public function setLayouts($layouts)
    {
        $this->layouts = $layouts;

        return $this;
    }
}
