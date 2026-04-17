<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
class TreeToolbarLinkCore extends TreeToolbarButtonCore implements ITreeToolbarButtonCore
{
    protected $_template = 'tree_toolbar_link.tpl';

    public function __construct($label, $link, $action = null, $iconClass = null)
    {
        parent::__construct($label);

        $this->setLink($link);
        $this->setAction($action);
        $this->setIconClass($iconClass);
    }

    public function setAction($value)
    {
        return $this->setAttribute('action', $value);
    }

    public function getAction()
    {
        return $this->getAttribute('action');
    }

    public function setIconClass($value)
    {
        return $this->setAttribute('icon_class', $value);
    }

    public function getIconClass()
    {
        return $this->getAttribute('icon_class');
    }

    public function setLink($value)
    {
        return $this->setAttribute('link', $value);
    }

    public function getLink()
    {
        return $this->getAttribute('link');
    }
}
