<?php
/**
 * 2007-2018 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShopBundle\Security\Voter;

use Access;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class PageVoter extends Voter
{
    const CREATE = 'create';

    const UPDATE = 'update';

    const DELETE = 'delete';

    const READ   = 'read';

    const LEVEL_DELETE   = 4;

    const LEVEL_UPDATE   = 2;

    const LEVEL_CREATE   = 3;

    const LEVEL_READ   = 1;

    /**
     * @param string $attribute
     * @param mixed $subject
     * @return bool
     */
    protected function supports($attribute, $subject)
    {
        if (!in_array($attribute, array(self::CREATE, self::UPDATE, self::DELETE, self::READ))) {
            return false;
        }

        return true;
    }

    /**
     * @param string $attribute
     * @param mixed $subject
     * @param TokenInterface $token
     * @return bool
     */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        $user = $token->getUser();
        $employeeProfileId = $user->getData()->id_profile;
        $global =  $subject . $attribute;

        return $this->can($global, $employeeProfileId);
    }

    /**
     * @param $action
     * @param $employeeProfileId
     * @return bool
     */
    protected function can($action, $employeeProfileId)
    {
        return Access::isGranted('ROLE_MOD_TAB_' . strtoupper($action), $employeeProfileId);
    }
}
