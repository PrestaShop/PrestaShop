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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */
declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Session;

use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\Session\Storage\PhpBridgeSessionStorage;

class SessionHandler implements SessionHandlerInterface
{
    /**
     *  @var Session
     */
    protected $session;

    /**
     * @var int
     */
    protected $lifetime;

    /**
     * @var bool
     */
    protected $isSecure;

    /**
     * @var string
     */
    protected $sameSite;

    /**
     * @var string
     */
    protected $path;

    public function __construct(
        int $lifetime,
        bool $isSecure,
        string $sameSite,
        string $shopUri
    ) {
        $this->lifetime = $lifetime;
        $this->isSecure = $isSecure;
        $this->sameSite = $sameSite;

        // Same behaviour as Cookie class
        $this->path = trim($shopUri, '/\\') . '/';
        if ($this->path[0] != '/') {
            $this->path = '/' . $this->path;
        }

        $this->path = rawurlencode($this->path);
        $this->path = str_replace(['%2F', '%7E', '%2B', '%26'], ['/', '~', '+', '&'], $this->path);
    }

    /**
     * {@inheritdoc}
     */
    public function getSession(): ?SessionInterface
    {
        return $this->session;
    }

    /**
     * {@inheritdoc}
     */
    public function init(): void
    {
        if ($this->isSessionDisabled() || $this->isSessionStarted()) {
            return;
        }

        /*
         * The alternative signature supporting an options array is only available since
         * PHP 7.3.0, before there is no support for SameSite attribute.
         */
        if (PHP_VERSION_ID < 70300) {
            session_set_cookie_params(
                $this->lifetime,
                $this->path . ';SameSite=' . $this->sameSite,
                '',
                $this->isSecure,
                true
            );
        } else {
            session_set_cookie_params([
                'lifetime' => $this->lifetime,
                'path' => $this->path,
                'secure' => $this->isSecure,
                'httponly' => true,
                'samesite' => $this->sameSite,
            ]);
        }

        $this->session = new Session(new PhpBridgeSessionStorage());
        $this->session->start();
    }

    /**
     * Is session disabled
     *
     * @return bool
     */
    protected function isSessionDisabled(): bool
    {
        return $this->getSessionStatus() === PHP_SESSION_DISABLED;
    }

    /**
     * Is session started
     *
     * @return bool
     */
    protected function isSessionStarted(): bool
    {
        return $this->getSessionStatus() === PHP_SESSION_ACTIVE;
    }

    /**
     * Get Session status
     *
     * @return int
     */
    protected function getSessionStatus(): int
    {
        return session_status();
    }
}
