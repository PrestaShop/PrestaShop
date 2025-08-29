<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
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
