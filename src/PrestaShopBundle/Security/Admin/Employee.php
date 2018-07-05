<?php
/**
 * 2007-2018 PrestaShop.
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

namespace PrestaShopBundle\Security\Admin;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\EquatableInterface;

/**
 * Class Employee is used for Symfony security components to authenticate the user.
 */
class Employee implements UserInterface, EquatableInterface
{
    /**
     * @var string
     */
    private $username;
    /**
     * @var string
     */
    private $password;
    /**
     * @var string
     */
    private $salt;
    /**
     * @var array
     */
    private $roles = array();

    private $data;

    /**
     * Constructor.
     *
     * @param object $data The employee legacy object
     */
    public function __construct($data)
    {
        $this->username = $data->email;
        $this->password = $data->passwd;
        $this->salt = '';
        $this->data = $data;
    }

    public function __toString()
    {
        return $this->username;
    }

    /**
     * Returns roles for the current employee.
     *
     * @return array
     */
    public function getRoles()
    {
        return $this->roles;
    }

    /**
     * Get typed password.
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * The salt used to hash the password.
     *
     * @return string
     */
    public function getSalt()
    {
        return $this->salt;
    }

    /**
     * Get the login of the current employee.
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Get the data parameter of the current employee.
     *
     * @return object
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Used by Symfony to ensure credentials are removed when logout.
     */
    public function eraseCredentials()
    {
    }

    /**
     * @param array $roles
     *
     * @return Employee
     */
    public function setRoles(array $roles)
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * Test equality between two Employee entities
     * (instance of class, password, salt and username).
     *
     * @param UserInterface $user
     *
     * @return bool
     */
    public function isEqualTo(UserInterface $user)
    {
        if (!$user instanceof Employee) {
            return false;
        }

        if ($this->password !== $user->getPassword()) {
            return false;
        }

        if ($this->salt !== $user->getSalt()) {
            return false;
        }

        if ($this->username !== $user->getUsername()) {
            return false;
        }

        return true;
    }
}
