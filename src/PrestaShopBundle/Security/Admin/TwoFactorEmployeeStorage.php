<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Security\Admin;

use PrestaShopBundle\Entity\Repository\EmployeeRepository;

final class TwoFactorEmployeeStorage
{
    public function __construct(
        private readonly EmployeeRepository $repository,
    ) {
    }

    /** @return array{enabled: bool, secret: string|null} */
    public function getByEmail(string $email): array
    {
        return $this->repository->getTwoFactorDataByEmail($email);
    }
}
