<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Utility;

use PrestaShopBundle\Security\Voter\PageVoter as BaseVoter;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class PageVoter extends BaseVoter
{
    /**
     * @param string $attribute
     * @param mixed $subject
     * @param TokenInterface $token
     *
     * @return bool
     */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token): bool
    {
        return true;
    }
}
