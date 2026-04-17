<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace Tests\Unit\PrestaShopBundle\Security\OAuth2\Repository;

use League\OAuth2\Server\Entities\ClientEntityInterface;
use PHPUnit\Framework\TestCase;
use PrestaShopBundle\Security\OAuth2\Repository\AccessTokenRepository;

class AccessTokenRepositoryTest extends TestCase
{
    /**
     * @var AccessTokenRepository
     */
    private $accessTokenRepository;

    public function setUp(): void
    {
        $this->accessTokenRepository = new AccessTokenRepository();
        parent::setUp();
    }

    public function testGetNewToken(): void
    {
        $token = $this->accessTokenRepository->getNewToken($this->createMock(ClientEntityInterface::class), [], 'userIdentifier');
        $this->assertSame('userIdentifier', $token->getUserIdentifier());
        $this->assertTrue($token->getClient() instanceof ClientEntityInterface);
    }
}
