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

namespace PrestaShopBundle\Security\Voter;

use PrestaShop\PrestaShop\Core\Security\AccessCheckerInterface;
use PrestaShop\PrestaShop\Core\Security\Permission;
use PrestaShopBundle\Security\Admin\Employee;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**
 * Decides on access rights to a Page.
 */
class PageVoter extends Voter
{
    /**
     * @deprecated since 9.0
     */
    public const CREATE = Permission::CREATE;

    /**
     * @deprecated since 9.0
     */
    public const UPDATE = Permission::UPDATE;

    /**
     * @deprecated since 9.0
     */
    public const DELETE = Permission::DELETE;

    /**
     * @deprecated since 9.0
     */
    public const READ = Permission::READ;

    /**
     * @deprecated since 9.0
     */
    public const LEVEL_DELETE = Permission::LEVEL_DELETE;

    /**
     * @deprecated since 9.0
     */
    public const LEVEL_UPDATE = Permission::LEVEL_UPDATE;

    /**
     * @deprecated since 9.0
     */
    public const LEVEL_CREATE = Permission::LEVEL_CREATE;

    /**
     * @deprecated since 9.0
     */
    public const LEVEL_READ = Permission::LEVEL_READ;

    /**
     * @var AccessCheckerInterface
     */
    private $accessChecker;

    public function __construct(AccessCheckerInterface $accessChecker)
    {
        $this->accessChecker = $accessChecker;
    }

    /**
     * Indicates if this voter should pronounce on this attribute and subject.
     *
     * @param string $attribute Rights to test
     * @param mixed $subject Subject to secure (a controller name)
     *
     * @return bool
     */
    protected function supports($attribute, $subject): bool
    {
        return in_array($attribute, [
            Permission::CREATE,
            Permission::UPDATE,
            Permission::DELETE,
            Permission::READ,
        ], true);
    }

    /**
     * @param string $attribute Access right to test
     * @param string $subject Controller name
     * @param TokenInterface $token
     *
     * @return bool
     */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token): bool
    {
        /** @var Employee $user */
        $user = $token->getUser();
        $employeeProfileId = $user->getData()->id_profile;
        $action = $this->buildAction((string) $subject, (string) $attribute);

        return $this->accessChecker->isEmployeeGranted($action, (int) $employeeProfileId);
    }

    /**
     * Builds the action name by joining subject and attribute.
     *
     * @param string $subject Subject the attribute is performed onto (usually a controller name)
     * @param string $attribute
     *
     * @return string
     */
    private function buildAction(string $subject, string $attribute): string
    {
        $action = $subject;

        // add underscore to join if needed
        if (!str_ends_with($action, '_')) {
            $action .= '_';
        }

        return $action . $attribute;
    }
}
