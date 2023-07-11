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

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Context;

use Context;
use Cookie;
use PrestaShop\PrestaShop\Core\Exception\InvalidArgumentException;

class CookieContextBuilder
{
    private ?int $cookieLifetime = null;

    private ?string $cookieName = null;

    private string $cookiePath = '';

    private bool $forceSsl = false;

    private ?CookieContext $cookieContext = null;

    public function build(): CookieContext
    {
        // CookieContext is built only once, for performance issue but also because the replacement of the Context->cookie should only be done once
        if ($this->cookieContext) {
            return $this->cookieContext;
        }

        if (null === $this->cookieLifetime) {
            throw new InvalidArgumentException(sprintf(
                'Cannot build cookie context as no cookieLifetime has been defined you need to call %s::setCookieLifetime to define it before building the cookie context',
                self::class
            ));
        }

        if (null === $this->cookieName) {
            throw new InvalidArgumentException(sprintf(
                'Cannot build cookie context as no cookieName has been defined you need to call %s::setCookieName to define it before building the cookie context',
                self::class
            ));
        }

        // The build is in charge of creating the cookie properly, it will in time be the only building it (and later we probably will refacto it not to rely on
        // this legacy cookie), but in the meantime we still need live with the legacy context and keeping two instances of the cookie is problematic because they
        // wouldn't be synced and some values would be overwritten between one another. Esepecially since the cookie automatically writes itself via __destruct at
        // the end of the process. That's the reason why we need to disable the writing of the Context's cookie (if present) and replace it by our own
        if (Context::getContext()->cookie) {
            Context::getContext()->cookie->disallowWriting();
            unset(Context::getContext()->cookie);
        }

        $cookie = new Cookie($this->cookieName, $this->cookiePath, $this->cookieLifetime, null, false, $this->forceSsl);
        Context::getContext()->cookie = $cookie;

        $this->cookieContext = new CookieContext($cookie);

        return $this->cookieContext;
    }

    public function setCookieLifetime(int $cookieLifetime): self
    {
        $this->cookieLifetime = $cookieLifetime;

        return $this;
    }

    /**
     * @param string|null $cookieName
     *
     * @return static
     */
    public function setCookieName(?string $cookieName): self
    {
        $this->cookieName = $cookieName;

        return $this;
    }

    public function setCookiePath(string $cookiePath): self
    {
        $this->cookiePath = $cookiePath;

        return $this;
    }

    public function setForceSsl(bool $forceSsl): self
    {
        $this->forceSsl = $forceSsl;

        return $this;
    }
}
