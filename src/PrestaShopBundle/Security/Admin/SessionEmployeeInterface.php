<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Security\Admin;

/**
 * This interface represents the elements that are available in the serialized Employee in the session.
 * To avoid any confusion we created a dedicated interface, these are the element that are accessible
 * just by parsing the session data before the security user is refreshed from database. This interface
 * is ued in early listeners and services that depend on the SessionEmployeeProvider.
 *
 * @internal
 */
interface SessionEmployeeInterface
{
    public function getId(): int;

    public function getUserIdentifier(): string;

    public function getPassword(): string;

    public function getProfileId(): int;

    public function getDefaultLocale(): string;
}
