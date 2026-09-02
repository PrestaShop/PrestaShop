<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Security\OAuth2\Entity;

use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Entities\Traits\ClientTrait;
use League\OAuth2\Server\Entities\Traits\EntityTrait;

/**
 * This class is the Client entity managed by ClientRepository
 *
 * @experimental
 */
class Client implements ClientEntityInterface
{
    use ClientTrait;
    use EntityTrait;
    protected ?int $lifetime;

    public function __construct()
    {
        // Client Credential Grant allow only confidential clients (rfc6749 section 4.4)
        $this->isConfidential = true;
    }

    public function getLifetime(): ?int
    {
        return $this->lifetime;
    }

    public function setLifetime(?int $lifetime): self
    {
        $this->lifetime = $lifetime;

        return $this;
    }
}
