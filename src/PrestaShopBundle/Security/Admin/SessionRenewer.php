<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Security\Admin;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Csrf\TokenStorage\ClearableTokenStorageInterface;

/**
 * Because PS don't use Symfony login feature, we use this service to fix CVE-2022-24895. This class will be deprecated
 * when BO login/logout will use full Symfony process
 *
 * @internal
 */
final class SessionRenewer
{
    /**
     * @param ClearableTokenStorageInterface $storage
     * @param RequestStack $requestStack
     */
    public function __construct(
        private readonly ClearableTokenStorageInterface $storage,
        private readonly RequestStack $requestStack
    ) {
    }

    /**
     * Change PHPSESSID and clear tokens registered in session
     *
     * @return void
     */
    public function renew(): void
    {
        if (!$this->requestStack->getSession()->isStarted()) {
            $this->requestStack->getSession()->start();
        }

        $this->requestStack->getSession()->migrate(true);
        $this->storage->clear();
    }
}
