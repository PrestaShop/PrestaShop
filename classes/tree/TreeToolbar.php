<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
class TreeToolbarCore implements ITreeToolbarCore
{
    public const DEFAULT_TEMPLATE_DIRECTORY = 'helpers/tree';
    public const DEFAULT_TEMPLATE = 'tree_toolbar.tpl';

    private $_actions;
    private $_context;
    private $_data;
    private $_template;
    private $_template_directory;

    public function __toString()
    {
        return $this->render();
    }

    public function setActions($actions)
    {
        if (!is_array($actions) && !$actions instanceof Traversable) {
            throw new PrestaShopException('Action value must be an traversable array');
        }

        foreach ($actions as $action) {
            $this->addAction($action);
        }
    }

    public function getActions()
    {
        if (!isset($this->_actions)) {
            $this->_actions = [];
        }

        return $this->_actions;
    }

    public function setContext($value)
    {
        $this->_context = $value;

        return $this;
    }

    public function getContext()
    {
        if (!isset($this->_context)) {
            $this->_context = Context::getContext();
        }

        return $this->_context;
    }

    public function setData($value)
    {
        if (!is_array($value) && !$value instanceof Traversable) {
            throw new PrestaShopException('Data value must be an traversable array');
        }

        $this->_data = $value;

        return $this;
    }

    public function getData()
    {
        return $this->_data;
    }

    public function setTemplate($value)
    {
        $this->_template = $value;

        return $this;
    }

    public function getTemplate()
    {
        if (!isset($this->_template)) {
            $this->setTemplate(self::DEFAULT_TEMPLATE);
        }

        return $this->_template;
    }

    public function setTemplateDirectory($value)
    {
        $this->_template_directory = $this->_normalizeDirectory($value);

        return $this;
    }

    public function getTemplateDirectory()
    {
        if (!isset($this->_template_directory)) {
            $this->_template_directory = $this->_normalizeDirectory(
                self::DEFAULT_TEMPLATE_DIRECTORY
            );
        }

        return $this->_template_directory;
    }

    public function getTemplateFile($template)
    {
        if (preg_match_all('/((?:^|[A-Z])[a-z]+)/', get_class($this->getContext()->controller), $matches) !== false) {
            $controllerName = strtolower($matches[0][1]);
        }

        if ($this->getContext()->controller instanceof ModuleAdminController && file_exists($this->_normalizeDirectory(
            $this->getContext()->controller->getTemplatePath()
        ) . $this->getTemplateDirectory() . $template)) {
            return $this->_normalizeDirectory($this->getContext()->controller->getTemplatePath())
                . $this->getTemplateDirectory() . $template;
        } elseif ($this->getContext()->controller instanceof AdminController && isset($controllerName)
            && file_exists($this->_normalizeDirectory($this->getContext()->smarty->getTemplateDir(0)) . 'controllers'
                . DIRECTORY_SEPARATOR . $controllerName . DIRECTORY_SEPARATOR . $this->getTemplateDirectory() . $template)) {
            return $this->_normalizeDirectory($this->getContext()->smarty->getTemplateDir(0)) . 'controllers'
                . DIRECTORY_SEPARATOR . $controllerName . DIRECTORY_SEPARATOR . $this->getTemplateDirectory() . $template;
        } elseif (file_exists($this->_normalizeDirectory($this->getContext()->smarty->getTemplateDir(1))
                . $this->getTemplateDirectory() . $template)) {
            return $this->_normalizeDirectory($this->getContext()->smarty->getTemplateDir(1))
                    . $this->getTemplateDirectory() . $template;
        } elseif (file_exists($this->_normalizeDirectory($this->getContext()->smarty->getTemplateDir(0))
                . $this->getTemplateDirectory() . $template)) {
            return $this->_normalizeDirectory($this->getContext()->smarty->getTemplateDir(0))
                . $this->getTemplateDirectory() . $template;
        } else {
            return $this->getTemplateDirectory() . $template;
        }
    }

    /**
     * @param ITreeToolbarButtonCore $action
     *
     * @return $this
     *
     * @throws PrestaShopException
     */
    public function addAction($action)
    {
        if (!is_object($action)) {
            throw new PrestaShopException('Action must be a class object');
        }

        $reflection = new ReflectionClass($action);

        if (!$reflection->implementsInterface('ITreeToolbarButtonCore')) {
            throw new PrestaShopException('Action class must implements ITreeToolbarButtonCore interface');
        }

        if (!isset($this->_actions)) {
            $this->_actions = [];
        }

        if (isset($this->_template_directory)) {
            $action->setTemplateDirectory($this->getTemplateDirectory());
        }

        $this->_actions[] = $action;

        return $this;
    }

    public function removeActions()
    {
        $this->_actions = null;

        return $this;
    }

    public function render()
    {
        foreach ($this->getActions() as $action) {
            /* @var ITreeToolbarButton $action */
            $action->setAttribute('data', $this->getData());
        }

        return $this->getContext()->smarty->createTemplate(
            $this->getTemplateFile($this->getTemplate()),
            $this->getContext()->smarty
        )->assign('actions', $this->getActions())->fetch();
    }

    private function _normalizeDirectory($directory)
    {
        $last = $directory[strlen($directory) - 1];

        if (in_array($last, ['/', '\\'])) {
            $directory[strlen($directory) - 1] = DIRECTORY_SEPARATOR;

            return $directory;
        }

        $directory .= DIRECTORY_SEPARATOR;

        return $directory;
    }
}
