<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Security\OAuth2\Entity;

use League\OAuth2\Server\Entities\AccessTokenEntityInterface;
use League\OAuth2\Server\Entities\Traits\AccessTokenTrait;
use League\OAuth2\Server\Entities\Traits\EntityTrait;
use League\OAuth2\Server\Entities\Traits\TokenEntityTrait;

/**
 * This class is the AccessToken entity managed by AccessTokenRepository
 *
 * @experimental
 */
class AccessToken implements AccessTokenEntityInterface
{
    /*
     * Default League\OAuth AccessToken implementation
     */
    use AccessTokenTrait;
    use TokenEntityTrait;
    use EntityTrait;
}
