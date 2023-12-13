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

namespace Tests\Integration\PrestaShopBundle\Admin\Security;

use PrestaShopBundle\Security\Admin\SessionRenewer;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Csrf\CsrfTokenManager;
use Tests\Integration\Utility\ContextMockerTrait;

class SessionRenewerTest extends WebTestCase
{
    use ContextMockerTrait;
    /**
     * @var CsrfTokenManager
     */
    private $sessionTokenManager;

    /**
     * @var SessionInterface
     */
    private $session;

    /**
     * @var object|SessionRenewer|null
     */
    private $sessionRenewer;

    protected function setUp(): void
    {
        $client = parent::createClient();
        static::mockContext();

        $router = self::$kernel->getContainer()->get('router');
        $client->request('GET', $router->generate('admin_title_index'));

        $this->sessionTokenManager = $client->getContainer()->get('security.csrf.token_manager');
        $this->session = $client->getContainer()->get('request_stack')->getSession();
        $this->sessionRenewer = $client->getContainer()->get(SessionRenewer::class);
    }

    public function testRenew(): void
    {
        $this->session->start();
        $originalSessionId = $this->session->getId();
        $token = $this->sessionTokenManager->getToken('foo');
        self::assertEquals($originalSessionId, $this->session->getId());
        self::assertTrue($this->sessionTokenManager->isTokenValid($token));
        $this->sessionRenewer->renew();
        self::assertNotEquals($originalSessionId, $this->session->getId());
        self::assertFalse($this->sessionTokenManager->isTokenValid($token));
    }
}
