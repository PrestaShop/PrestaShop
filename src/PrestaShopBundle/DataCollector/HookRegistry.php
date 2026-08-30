<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\DataCollector;

use ModuleCore;
use PrestaShop\PrestaShop\Core\Module\Legacy\ModuleInterface;
use Twig\Template;

/**
 * Collect all hooks information dispatched during a request.
 *
 * One entry per hook name, addressed by name on every mutator.
 * Status promotion is asymmetric: CALLED is sticky and cannot be downgraded.
 */
final class HookRegistry
{
    public const HOOK_NOT_CALLED = 'notCalled';
    public const HOOK_NOT_REGISTERED = 'notRegistered';
    public const HOOK_CALLED = 'called';

    /**
     * Frames whose class is — or inherits from — one of these are part of the
     * dispatch machinery and skipped when resolving the originator. Inheritance
     * matters because legacy classes are typically extended by an empty `Xxx`
     * subclass of `XxxCore`, and modules may override dispatcher classes.
     */
    private const MACHINERY_CLASSES = [
        'HookCore', // legacy Hook class (Hook extends HookCore via override pattern)
        'PrestaShopBundle\\DataCollector\\HookRegistry',
        'PrestaShopBundle\\Twig\\HookExtension',
        'PrestaShop\\PrestaShop\\Adapter\\Hook\\HookDispatcher',
        'PrestaShop\\PrestaShop\\Adapter\\LegacyHookSubscriber',
        'PrestaShop\\PrestaShop\\Core\\Hook\\HookDispatcher',
    ];

    /**
     * Namespace prefixes for vendor code we treat as machinery as a whole.
     * Used where inheritance matching is impractical (the SF event dispatcher
     * has many concrete classes scattered across the namespace).
     */
    private const MACHINERY_NAMESPACE_PREFIXES = [
        'Symfony\\Component\\EventDispatcher\\',
    ];

    /**
     * @var array<string, array{name: string, status: string, dispatch_count: int, calls_count: int, args: array, location: string, modules: array<string, array{callback?: array{args: array}, widget?: array{args: array}}>}>
     */
    private array $hooks = [];

    /**
     * Record that a hook was dispatched. Creates the entry on first dispatch,
     * increments `dispatch_count` and refreshes `args`/`location` on subsequent ones.
     */
    public function hookDispatched(string $hookName, array $hookArguments): void
    {
        $frame = $this->resolveOriginatorFrame();
        $location = sprintf('%s:%s', $frame['file'], $frame['line']);

        if (!isset($this->hooks[$hookName])) {
            $this->hooks[$hookName] = [
                'name' => $hookName,
                'status' => self::HOOK_NOT_CALLED,
                'dispatch_count' => 0,
                'calls_count' => 0,
                'args' => $hookArguments,
                'location' => $location,
                'modules' => [],
            ];
        }

        ++$this->hooks[$hookName]['dispatch_count'];
        $this->hooks[$hookName]['args'] = $hookArguments;
        $this->hooks[$hookName]['location'] = $location;
    }

    /**
     * Mark a hook as not registered (no listeners / not in DB). Asymmetric:
     * never downgrades from CALLED. Silently no-ops if the hook was never dispatched.
     */
    public function hookWasNotRegistered(string $hookName): void
    {
        if (!isset($this->hooks[$hookName])) {
            return;
        }
        if (self::HOOK_NOT_CALLED === $this->hooks[$hookName]['status']) {
            $this->hooks[$hookName]['status'] = self::HOOK_NOT_REGISTERED;
        }
    }

    /**
     * A module callback was executed. Promotes status to CALLED and increments
     * `calls_count`. Silently no-ops if the hook was never dispatched.
     *
     * @param ModuleCore $module
     */
    public function hookedByCallback(ModuleInterface $module, array $args, string $hookName): void
    {
        if (!isset($this->hooks[$hookName])) {
            return;
        }
        $this->hooks[$hookName]['status'] = self::HOOK_CALLED;
        ++$this->hooks[$hookName]['calls_count'];
        $this->hooks[$hookName]['modules'][$module->name]['callback'] = ['args' => $args];
    }

    /**
     * A module widget was rendered. Promotes status to CALLED and increments
     * `calls_count`. Silently no-ops if the hook was never dispatched.
     *
     * @param ModuleCore $module
     */
    public function hookedByWidget(ModuleInterface $module, array $args, string $hookName): void
    {
        if (!isset($this->hooks[$hookName])) {
            return;
        }
        $this->hooks[$hookName]['status'] = self::HOOK_CALLED;
        ++$this->hooks[$hookName]['calls_count'];
        $this->hooks[$hookName]['modules'][$module->name]['widget'] = ['args' => $args];
    }

    /**
     * @return array<string, array> hooks whose code actually ran
     */
    public function getCalledHooks(): array
    {
        return array_filter($this->hooks, fn (array $hook): bool => self::HOOK_CALLED === $hook['status']);
    }

    /**
     * @return array<string, array> hooks dispatched but without any module producing output
     */
    public function getNotCalledHooks(): array
    {
        return array_filter($this->hooks, fn (array $hook): bool => self::HOOK_NOT_CALLED === $hook['status']);
    }

    /**
     * @return array<string, array> hooks dispatched without listeners (Symfony events not in DB)
     */
    public function getNotRegisteredHooks(): array
    {
        return array_filter($this->hooks, fn (array $hook): bool => self::HOOK_NOT_REGISTERED === $hook['status']);
    }

    /**
     * @return array<string, array> all dispatched hooks, regardless of status
     */
    public function getHooks(): array
    {
        return $this->hooks;
    }

    /**
     * Walk the backtrace and return the first frame outside the dispatch machinery.
     *
     * For frames that originate from a compiled Twig template, the source `.twig`
     * file path and source line are returned instead of the compiled cache path.
     *
     * @return array{file: string, line: int}
     */
    private function resolveOriginatorFrame(): array
    {
        // PROVIDE_OBJECT is required because IGNORE_ARGS alone strips the `object`
        // key from the backtrace frames — and we need that key for Twig template
        // resolution and inheritance-aware machinery detection.
        $backtrace = debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT | DEBUG_BACKTRACE_IGNORE_ARGS, 15);
        foreach ($backtrace as $frame) {
            if (!isset($frame['file'])) {
                continue;
            }

            $twigFrame = $this->resolveTwigTemplateFrame($frame);
            if (null !== $twigFrame) {
                return $twigFrame;
            }

            if ($this->isMachineryFrame($frame)) {
                continue;
            }

            return [
                'file' => $frame['file'],
                'line' => (int) ($frame['line'] ?? 0),
            ];
        }

        return ['file' => 'unknown', 'line' => 0];
    }

    /**
     * If the frame belongs to a compiled Twig template, return the source
     * `.twig` path and the corresponding source line. Returns null otherwise.
     *
     * @return array{file: string, line: int}|null
     */
    private function resolveTwigTemplateFrame(array $frame): ?array
    {
        if (!isset($frame['object']) || !$frame['object'] instanceof Template) {
            return null;
        }

        $template = $frame['object'];
        $compiledLine = (int) ($frame['line'] ?? 0);
        $debugInfo = $template->getDebugInfo();

        return [
            'file' => $template->getSourceContext()->getName(),
            'line' => (int) ($debugInfo[$compiledLine] ?? 0),
        ];
    }

    private function isMachineryFrame(array $frame): bool
    {
        $class = $frame['class'] ?? '';
        if ('' === $class) {
            return false;
        }
        foreach (self::MACHINERY_NAMESPACE_PREFIXES as $prefix) {
            if (str_starts_with($class, $prefix)) {
                return true;
            }
        }
        foreach (self::MACHINERY_CLASSES as $machineryClass) {
            // is_a() with $allow_string=true accepts a class name and walks
            // the inheritance chain — so subclasses of machinery classes
            // (e.g. `Hook extends HookCore`) are recognised as machinery too.
            if (is_a($class, $machineryClass, true)) {
                return true;
            }
        }

        return false;
    }
}
