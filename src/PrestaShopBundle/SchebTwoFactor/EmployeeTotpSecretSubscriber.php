<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\SchebTwoFactor;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\PostLoadEventArgs;
use Doctrine\ORM\Event\PrePersistEventArgs;
use Doctrine\ORM\Events;
use PrestaShopBundle\Entity\Employee\Employee;

final class EmployeeTotpSecretSubscriber implements EventSubscriber
{
    public function __construct(
        private readonly TotpSecretEncryptor $encryptor
    ) {
    }

    public function getSubscribedEvents(): array
    {
        return [Events::postLoad, Events::prePersist];
    }

    public function postLoad(PostLoadEventArgs $args): void
    {
        $e = $args->getObject();

        if (!$e instanceof Employee) {
            return;
        }

        $enc = $e->getTwoFactorSecret();
        $e->setTwoFactorTotpSecretPlain($enc ? $this->encryptor->decrypt($enc) : null);
    }

    public function prePersist(PrePersistEventArgs $args): void
    {
        $e = $args->getObject();

        if (!$e instanceof Employee) {
            return;
        }

        $plain = $e->getTwoFactorTotpSecretPlain();
        $e->setTwoFactorSecret($plain ? $this->encryptor->encrypt($plain) : null);
    }
}
