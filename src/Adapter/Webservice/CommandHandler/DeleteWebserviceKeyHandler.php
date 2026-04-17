<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Webservice\CommandHandler;

use PrestaShop\PrestaShop\Adapter\Webservice\WebserviceKeyEraser;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Webservice\Command\DeleteWebserviceKeyCommand;
use PrestaShop\PrestaShop\Core\Domain\Webservice\CommandHandler\DeleteWebserviceKeyHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Webservice\Exception\CannotDeleteWebserviceException;

/**
 * Handles command that delete webservice
 */
#[AsCommandHandler]
class DeleteWebserviceKeyHandler extends AbstractWebserviceKeyHandler implements DeleteWebserviceKeyHandlerInterface
{
    private WebserviceKeyEraser $webserviceKeyEraser;

    /**
     * @param WebserviceKeyEraser $webserviceKeyEraser
     */
    public function __construct(WebserviceKeyEraser $webserviceKeyEraser)
    {
        $this->webserviceKeyEraser = $webserviceKeyEraser;
    }

    /**
     * {@inheritdoc}
     *
     * @throws CannotDeleteWebserviceException
     */
    public function handle(DeleteWebserviceKeyCommand $command): void
    {
        $errors = $this->webserviceKeyEraser->erase([$command->getWebserviceKeyId()->getValue()]);
        if (!empty($errors)) {
            throw new CannotDeleteWebserviceException($errors);
        }
    }
}
