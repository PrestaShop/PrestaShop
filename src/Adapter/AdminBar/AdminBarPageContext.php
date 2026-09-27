<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\AdminBar;

/**
 * Carries the Front Office page details required to resolve Admin Bar actions.
 *
 * Module ownership and controller name are exposed as stable identifiers so extensions do not
 * need to depend on concrete Front Office controller classes.
 */
final class AdminBarPageContext
{
    public function __construct(
        private readonly object $controller,
        private readonly string $pageName,
        private readonly ?int $resourceId = null,
        private readonly ?string $resourceType = null,
        private readonly ?string $ownerModule = null,
        private readonly ?string $controllerName = null,
    ) {
    }

    public function getController(): object
    {
        return $this->controller;
    }

    public function getPageName(): string
    {
        return $this->pageName;
    }

    public function getResourceId(): ?int
    {
        return $this->resourceId;
    }

    public function getResourceType(): ?string
    {
        return $this->resourceType;
    }

    public function getOwnerModule(): ?string
    {
        return $this->ownerModule;
    }

    public function getControllerName(): string
    {
        return $this->controllerName ?? $this->pageName;
    }
}
