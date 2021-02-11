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

use Access;
use PrestaShopBundle\Security\Admin\Employee;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**
 * Decides on access rights to a Page.
 */
class PageVoter extends Voter
{
    public const CREATE = 'create';

    public const UPDATE = 'update';

    public const DELETE = 'delete';

    public const READ = 'read';

    public const LEVEL_DELETE = 4;

    public const LEVEL_UPDATE = 2;

    public const LEVEL_CREATE = 3;

    public const LEVEL_READ = 1;

    /**
     * Indicates if this voter should pronounce on this attribute and subject.
     *
     * @param string $attribute Rights to test
     * @param mixed $subject Subject to secure (a controller name)
     *
     * @return bool
     */
    protected function supports($attribute, $subject)
    {
        return in_array($attribute, [self::CREATE, self::UPDATE, self::DELETE, self::READ]);
    }

    /**
     * @param string $attribute Access right to test
     * @param string $subject Controller name
     * @param TokenInterface $token
     *
     * @return bool
     */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        /** @var Employee $user */
        $user = $token->getUser();
        $employeeProfileId = $user->getData()->id_profile;
        $action = $this->buildAction($subject, $attribute);

        return $this->can($action, $employeeProfileId);
    }

    /**
     * Checks if the provided user profile is allowed to perform the requested action.
     *
     * @param string $action
     * @param int $employeeProfileId
     *
     * @return bool
     *
     * @throws \Exception
     */
    protected function can($action, $employeeProfileId)
    {
        return Access::isGranted('ROLE_MOD_TAB_' . strtoupper($action), $employeeProfileId);
    }

    /**
     * Builds the action name by joining subject and attribute.
     *
     * @param string $subject Subject the attribute is performed onto (usually a controller name)
     * @param string $attribute
     *
     * @return string
     */
    private function buildAction($subject, $attribute)
    {
        $action = $subject;

        // add underscore to join if needed
        if (substr($action, -1) !== '_') {
            $action .= '_';
        }

        return $action . $attribute;
    }
}
