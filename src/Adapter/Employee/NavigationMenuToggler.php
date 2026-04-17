<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Employee;

use PrestaShop\PrestaShop\Adapter\LegacyContext;
use PrestaShop\PrestaShop\Core\Employee\NavigationMenuTogglerInterface;

/**
 * Class NavigationMenuToggler handles collapsing/expanding the navigation for context employee.
 */
final class NavigationMenuToggler implements NavigationMenuTogglerInterface
{
    /**
     * @var LegacyContext
     */
    private $legacyContext;

    /**
     * @param LegacyContext $legacyContext
     */
    public function __construct(LegacyContext $legacyContext)
    {
        $this->legacyContext = $legacyContext;
    }

    /**
     * {@inheritdoc}
     */
    public function toggleNavigationMenuInCookies(bool $shouldCollapse): void
    {
        $this->legacyContext->getContext()->cookie->collapse_menu = (int) $shouldCollapse;
        $this->legacyContext->getContext()->cookie->write();
    }
}
