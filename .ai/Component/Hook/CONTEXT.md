# Hook Component

## Purpose

Event/extension system that allows modules to react to and modify PrestaShop behavior: dispatching hooks with parameters, collecting rendered HTML output from multiple module listeners. Does not manage module registration — that is handled by the Module component.

## Layers

| Layer | Path |
|-------|------|
| Core contracts + value objects | `src/Core/Hook/` |
| Hook providers | `src/Core/Hook/Provider/` |
| Adapter dispatcher + CQRS handlers | `src/Adapter/Hook/` |

## Non-obvious patterns

- `HookDispatcher` prevents `stopPropagation()` — unlike Symfony events, all listeners always run; a module cannot short-circuit other modules
- Two dispatch modes: `dispatchHook()` (fire-and-forget) vs `dispatchRendering()` — the latter collects HTML output from all listeners into `RenderedHook::getContent()`
- `HookExtractor` + `HookParserVisitor` use AST-based extraction to auto-document hook calls from source files

## Canonical examples

- `src/Core/Hook/HookDispatcherInterface.php`
- `src/Adapter/Hook/HookDispatcher.php`
- Usage in grid: `src/Core/Grid/Definition/Factory/AbstractGridDefinitionFactory.php`

