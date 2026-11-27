<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataProvider;

use LogicException;
use PrestaShopBundle\Entity\Enum\CustomerB2bStatus;

final class CustomerB2bFormDataProvider implements FormDataProviderInterface
{
    /**
     * {@inheritDoc}
     */
    public function getData($id): array
    {
        throw new LogicException('Not implemented.'); // TODO: US3.3
    }

    /**
     * {@inheritDoc}
     */
    public function getDefaultData(): array
    {
        return [
            'use_existing_customer' => false,
            'send_password_setup_link' => true,
            'status' => CustomerB2bStatus::ACTIVE,
        ];
    }
}
