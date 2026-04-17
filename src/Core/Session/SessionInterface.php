<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Session;

/**
 * SessionInterface is used to store/access to the session token used by customers and employees
 */
interface SessionInterface
{
    /**
     * Returns session id
     *
     * @return int
     */
    public function getId();

    /**
     * Set session user id
     *
     * @param int $id
     *
     * @return void
     */
    public function setUserId($id);

    /**
     * Returns session user id
     *
     * @return int
     */
    public function getUserId();

    /**
     * Set session token
     *
     * @param string $string
     *
     * @return void
     */
    public function setToken($string);

    /**
     * Returns session token
     *
     * @return string
     */
    public function getToken();

    /**
     * Adds current object to the database.
     */
    public function add();

    /**
     * Deletes current object from database.
     */
    public function delete();
}
