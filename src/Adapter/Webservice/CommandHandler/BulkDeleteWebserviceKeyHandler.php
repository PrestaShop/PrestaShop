<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Webservice\CommandHandler;

use PrestaShop\PrestaShop\Adapter\Webservice\WebserviceKeyEraser;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Webservice\Command\BulkDeleteWebserviceKeyCommand;
use PrestaShop\PrestaShop\Core\Domain\Webservice\CommandHandler\BulkDeleteWebserviceKeyHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Webservice\Exception\CannotDeleteWebserviceException;

/**
 * Handles command that bulk delete webservices
 */
#[AsCommandHandler]
class BulkDeleteWebserviceKeyHandler extends AbstractWebserviceKeyHandler implements BulkDeleteWebserviceKeyHandlerInterface
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
     */
    public function handle(BulkDeleteWebserviceKeyCommand $command): void
    {
        foreach ($command->getWebserviceKeyIds() as $webserviceKeyId) {
            $errors = $this->webserviceKeyEraser->erase([
                $webserviceKeyId->getValue(),
            ]);
            if (!empty($errors)) {
                throw new CannotDeleteWebserviceException($errors);
            }
        }
    }
}
