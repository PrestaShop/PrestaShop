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

namespace Tests\Integration\PrestaShopBundle\Security;

use Symfony\Component\Routing\RouterInterface;
use Tests\TestCase\SymfonyIntegrationTestCase;

class LoginThrottlingTest extends SymfonyIntegrationTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    public function testRepeatedFailuresTemporarilyBlockSuccessfulLogin(): void
    {
        $pool = self::$kernel->getContainer()->get('cache.rate_limiter');
        $pool->clear();

        /** @var RouterInterface $router */
        $router = self::$kernel->getContainer()->get('router');
        $loginUrl = $router->generate('admin_login');

        $this->exhaustLoginAttempts($loginUrl, 5);

        $this->submitLoginForm($loginUrl, 'test');
        $crawler = $this->client->followRedirect();

        $this->assertSame('admin_login', $this->client->getRequest()->attributes->get('_route'));
        $this->assertSame(1, $crawler->filter('form#login_form')->count());
    }

    private function exhaustLoginAttempts(string $loginUrl, int $attempts): void
    {
        for ($i = 0; $i < $attempts; ++$i) {
            $this->submitLoginForm($loginUrl, 'wrong-password');
            $this->client->followRedirect();
        }
    }

    private function submitLoginForm(string $loginUrl, string $password): void
    {
        $this->client->request('GET', $loginUrl);
        $form = $this->client->getCrawler()->filter('form#login_form')->form([
            'email' => 'test@prestashop.com',
            'passwd' => $password,
        ]);
        $this->client->submit($form);
    }
}
