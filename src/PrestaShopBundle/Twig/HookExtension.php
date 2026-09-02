<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Twig;

use Exception;
use PrestaShop\PrestaShop\Adapter\Module\ModuleDataProvider;
use PrestaShop\PrestaShop\Core\Hook\HookDispatcherInterface;
use PrestaShop\PrestaShop\Core\Module\ModuleRepository;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

/**
 * This class is used by Twig_Environment and provide some methods callable from a twig template.
 */
class HookExtension extends AbstractExtension
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
        ?ModuleRepository $moduleRepository = null
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
        return [
            new TwigFilter('renderhook', [$this, 'renderHook'], ['is_safe' => ['html']]),
            new TwigFilter('renderhooksarray', [$this, 'renderHooksArray'], ['is_safe' => ['html']]),
            new TwigFilter('hooksarraycontent', [$this, 'hooksArrayContent']),
        ];
    }

    /**
     * Defines available functions.
     *
     * @return array Twig_SimpleFilter
     */
    public function getFunctions()
    {
        return [
            new TwigFunction('renderhook', [$this, 'renderHook'], ['is_safe' => ['html']]),
            new TwigFunction('renderhooksarray', [$this, 'renderHooksArray'], ['is_safe' => ['html']]),
            new TwigFunction('hooksarraycontent', [$this, 'hooksArrayContent']),
        ];
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
     * @return array[string] All listener's responses, ordered by the listeners' priorities
     *
     * @throws Exception if the hookName is missing
     */
    public function renderHooksArray($hookName, $hookParameters = [])
    {
        if ('' == $hookName) {
            throw new Exception('Hook name missing');
        }

        // The call to the render of the hooks is encapsulated into a ob management to avoid any call of echo from the
        // modules.
        ob_start();
        $renderedHook = $this->hookDispatcher->dispatchRenderingWithParameters($hookName, $hookParameters);
        $renderedHook->outputContent();
        ob_end_clean();

        $render = [];
        foreach ($renderedHook->getContent() as $module => $hookRender) {
            $moduleAttributes = $this->moduleRepository->getModule($module)->getAttributes();
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
     * @return string all listener's responses, concatenated in a simple string, ordered by the listeners' priorities
     *
     * @throws Exception if the hookName is missing
     */
    public function renderHook($hookName, array $hookParameters = [])
    {
        if ($hookName == '') {
            throw new Exception('Hook name missing');
        }

        return $this->hookDispatcher
            ->dispatchRenderingWithParameters($hookName, $hookParameters)
            ->outputContent();
    }

    /**
     * Return the concatenated content of a renderHooksArray response
     *
     * @param array $hooksArray the array returned by the renderHooksArray function
     *
     * @return string
     */
    public function hooksArrayContent($hooksArray)
    {
        if (!is_array($hooksArray)) {
            return '';
        }

        $content = '';

        foreach ($hooksArray as $hook) {
            $content .= $hook['content'] ?? '';
        }

        return $content;
    }
}
