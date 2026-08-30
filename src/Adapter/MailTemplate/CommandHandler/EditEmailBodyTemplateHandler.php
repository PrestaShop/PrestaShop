<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\MailTemplate\CommandHandler;

use PrestaShop\PrestaShop\Adapter\MailTemplate\EmailBodyTemplateRepository;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\MailTemplate\Command\EditEmailBodyTemplateCommand;
use PrestaShop\PrestaShop\Core\Domain\MailTemplate\CommandHandler\EditEmailBodyTemplateHandlerInterface;

/**
 * @internal
 */
#[AsCommandHandler]
class EditEmailBodyTemplateHandler implements EditEmailBodyTemplateHandlerInterface
{
    public function __construct(
        private readonly EmailBodyTemplateRepository $repository,
    ) {
    }

    public function handle(EditEmailBodyTemplateCommand $command): void
    {
        $this->repository->save(
            $command->getTemplateName()->getValue(),
            $command->getLocale(),
            $command->getSource(),
            $command->getHtmlContent(),
            $command->getTxtContent(),
        );
    }
}
