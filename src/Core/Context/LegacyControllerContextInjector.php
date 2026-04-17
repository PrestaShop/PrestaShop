<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Context;

use PrestaShop\PrestaShop\Adapter\ContextStateManager;

/**
 * This class is independent of the LegacyControllerContextBuilder, this way wa can inject LegacyControllerContext
 * into it and use the lazyness of the service in our favor. This service will be called via the kernel.controller
 * event from LegacyContextListener, by then LegacyControllerContextBuilder will have already been configured and
 * the service can be lazy constructed.
 *
 * We must rely on the lazy Symfony service and inject this one in particular or the service used in Symfony and the
 * one injected in legacy context won't be the same instance thus we wouldn't get the appropriate assets.
 */
class LegacyControllerContextInjector implements LegacyContextBuilderInterface
{
    public function __construct(
        private readonly ContextStateManager $contextStateManager,
        private readonly LegacyControllerContext $legacyControllerContext,
    ) {
    }

    public function buildLegacyContext(): void
    {
        // In legacy pages the AdminController class already sets the context's controller, which is a more accurate
        // candidate than our facade meant for backward compatibility, so we leave it untouched
        if ($this->contextStateManager->getContext()->controller) {
            return;
        }

        // We must fetch the service from the container to make sure Symfony service and legacy Context share the same
        // instance, this is critical to make sure we load the appropriate asset files injected from legacy code
        $this->contextStateManager->setController($this->legacyControllerContext);
    }
}
