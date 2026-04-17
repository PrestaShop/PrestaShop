<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Session;

use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\Session\Storage\PhpBridgeSessionStorage;

class SessionHandler implements SessionHandlerInterface
{
    /**
     * @var Session
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

        session_set_cookie_params([
            'lifetime' => $this->lifetime,
            'path' => $this->path,
            'secure' => $this->isSecure,
            'httponly' => true,
            'samesite' => $this->sameSite,
        ]);

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
