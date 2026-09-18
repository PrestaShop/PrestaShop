<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\AdminBar;

use Module;
use ModuleFrontController;
use Symfony\Contracts\Service\ServiceProviderInterface;

/**
 * Builds Admin Bar page contexts from Front Office controllers.
 *
 * It normalizes module ownership and controller names for the module action hook.
 */
final class AdminBarPageContextFactory
{
    /** @param ServiceProviderInterface<AdminBarResourceProviderInterface> $resourceProviders */
    public function __construct(private readonly ServiceProviderInterface $resourceProviders)
    {
    }

    public function create(object $controller): ?AdminBarPageContext
    {
        if (!method_exists($controller, 'getPageName')) {
            return null;
        }

        $pageName = $controller->getPageName();
        if (!is_string($pageName) || $pageName === '') {
            return null;
        }

        $ownerModule = null;
        $controllerName = $pageName;
        if ($controller instanceof ModuleFrontController && $controller->module instanceof Module) {
            $ownerModule = $controller->module->name;
            $modulePagePrefix = 'module-' . $ownerModule . '-';
            if (str_starts_with($pageName, $modulePagePrefix)) {
                $controllerName = substr($pageName, strlen($modulePagePrefix));
            }
        }

        if ($this->resourceProviders->has($controller::class)) {
            /** @var AdminBarResourceProviderInterface $resourceProvider */
            $resourceProvider = $this->resourceProviders->get($controller::class);
            $resource = $resourceProvider->getResource($controller);
            if ($resource !== null) {
                return new AdminBarPageContext(
                    $controller,
                    $pageName,
                    $resource->getId(),
                    $resource->getType(),
                    $ownerModule,
                    $controllerName,
                );
            }
        }

        return new AdminBarPageContext($controller, $pageName, null, null, $ownerModule, $controllerName);
    }
}
