<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Twig\Component\Legacy;

use AdminController;

/**
 * For legacy pages based on legacy admin controller we don't use the LegacyControllerContext which purpose is to replace
 * the legacy controller for backward compatibility. In this case a real legacy controller is already accessible via the
 * legacy context and should be preferred.
 *
 * Its data, and especially the CSS, JS files and JS definitions are more likely to be up-to-date.
 */
trait LegacyControllerTrait
{
    protected function getLegacyController(): AdminController
    {
        return $this->context->getContext()->controller;
    }

    protected function hasLegacyController(): bool
    {
        return $this->context->getContext()->controller instanceof AdminController;
    }
}
