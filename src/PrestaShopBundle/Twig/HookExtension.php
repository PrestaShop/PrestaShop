<?php
/**
 * 2007-2019 PrestaShop and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShopBundle\Twig;

use PrestaShop\PrestaShop\Adapter\Module\ModuleDataProvider;
use PrestaShop\PrestaShop\Core\Addon\Module\ModuleRepository;
use PrestaShop\PrestaShop\Core\Hook\HookDispatcherInterface;

/**
 * This class is used by Twig_Environment and provide some methods callable from a twig template.
 */
class HookExtension extends \Twig_Extension
{
    /**
     * @var HookDispatcherInterface
     */
    private $hookDispatcher;

    /**
     * @var ModuleDataProvider
     */
    private $moduleDataProvider;

    /**
     * @var ModuleRepository
     */
    private $moduleRepository;

    /**
     * Constructor.
     *
     * @param HookDispatcherInterface $hookDispatcher
     * @param ModuleDataProvider $moduleDataProvider
     */
    public function __construct(
        HookDispatcherInterface $hookDispatcher,
        ModuleDataProvider $moduleDataProvider,
        ModuleRepository $moduleRepository = null
    ) {
        $this->hookDispatcher = $hookDispatcher;
        $this->moduleDataProvider = $moduleDataProvider;
        $this->moduleRepository = $moduleRepository;
    }

    /**
     * Defines available filters.
     *
     * @return array Twig_SimpleFilter
     */
    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('renderhook', array($this, 'renderHook'), array('is_safe' => array('html'))),
            new \Twig_SimpleFilter('renderhooksarray', array($this, 'renderHooksArray'), array('is_safe' => array('html'))),
        );
    }

    /**
     * Defines available functions.
     *
     * @return array Twig_SimpleFilter
     */
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('renderhook', array($this, 'renderHook'), array('is_safe' => array('html'))),
            new \Twig_SimpleFunction('renderhooksarray', array($this, 'renderHooksArray'), array('is_safe' => array('html'))),
            new \Twig_SimpleFunction('hookcount', array($this, 'hookCount')),
        );
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'twig_hook_extension';
    }

    /**
     * Calls the HookDispatcher, and dispatch a RenderingHookEvent.
     *
     * The listeners will then return html data to display in the Twig template.
     *
     * @param string $hookName the name of the hook to trigger
     * @param array $hookParameters the parameters to send to the Hook
     *
     * @throws \Exception if the hookName is missing
     *
     * @return array[string] All listener's responses, ordered by the listeners' priorities
     */
    public function renderHooksArray($hookName, $hookParameters = array())
    {
        if ('' == $hookName) {
            throw new \Exception('Hook name missing');
        }

        // The call to the render of the hooks is encapsulated into a ob management to avoid any call of echo from the
        // modules.
        ob_start();
        $renderedHook = $this->hookDispatcher->dispatchRenderingWithParameters($hookName, $hookParameters);
        $renderedHook->outputContent();
        ob_clean();

        $render = [];
        foreach ($renderedHook->getContent() as $module => $hookRender) {
            $moduleAttributes = $this->moduleRepository->getModuleAttributes($module);
            $render[] = [
                'id' => $module,
                'name' => $this->moduleDataProvider->getModuleName($module),
                'content' => $hookRender,
                'attributes' => $moduleAttributes->all(),
            ];
        }

        return $render;
    }

    /**
     * Calls the HookDispatcher, and dispatch a RenderingHookEvent.
     *
     * The listeners will then return html data to display in the Twig template.
     *
     * @param string $hookName the name of the hook to trigger
     * @param array $hookParameters the parameters to send to the Hook
     *
     * @throws \Exception if the hookName is missing
     *
     * @return string all listener's responses, concatenated in a simple string, ordered by the listeners' priorities
     */
    public function renderHook($hookName, array $hookParameters = array())
    {
        if ($hookName == '') {
            throw new \Exception('Hook name missing');
        }

        return $this->hookDispatcher
            ->dispatchRenderingWithParameters($hookName, $hookParameters)
            ->outputContent();
    }

    /**
     * Count how many listeners will respond to the hook name.
     * Does not trigger the hook, so maybe some listeners could not add a response to the result.
     *
     * @param string $hookName
     *
     * @return number the listeners count that will respond to the hook name
     */
    public function hookCount($hookName)
    {
        return count($this->hookDispatcher->getListeners($hookName));
    }
}
