<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Session;

use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * SessionHandlerInterface is used to set PHP Sessions cookie parameters
 */
interface SessionHandlerInterface
{
    /**
     * Initialize session
     *
     * @return void
     */
    public function init(): void;

    /**
     * Return the current session
     *
     * @return ?SessionInterface
     */
    public function getSession(): ?SessionInterface;
}
