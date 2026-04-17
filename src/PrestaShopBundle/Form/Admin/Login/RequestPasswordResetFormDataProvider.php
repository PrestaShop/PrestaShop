<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Form\Admin\Login;

use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\Employee\Command\SendEmployeePasswordResetEmailCommand;
use PrestaShop\PrestaShop\Core\Form\FormDataProviderInterface;

class RequestPasswordResetFormDataProvider implements FormDataProviderInterface
{
    public function __construct(
        private readonly CommandBusInterface $commandBus,
    ) {
    }

    public function getData()
    {
        return [];
    }

    public function setData(array $data)
    {
        $this->commandBus->handle(new SendEmployeePasswordResetEmailCommand($data['email_forgot']));

        return [];
    }
}
