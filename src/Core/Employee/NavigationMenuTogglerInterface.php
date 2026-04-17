<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Employee;

/**
 * Interface NavigationMenuTogglerInterface describes an employee navigation menu toggler.
 */
interface NavigationMenuTogglerInterface
{
    /**
     * Toggle the navigation for employee (collapse/expand)
     *
     * @param bool $shouldCollapse if true - collapse the navigation, expand it otherwise
     */
    public function toggleNavigationMenuInCookies(bool $shouldCollapse): void;
}
