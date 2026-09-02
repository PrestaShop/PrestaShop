<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Security;

class Hashing
{
    public function hash(string $passwd, string $salt): string
    {
        return md5($salt . $passwd);
    }
}
