<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\MailTemplate\QueryHandler;

use PrestaShop\PrestaShop\Adapter\MailTemplate\EmailBodyTemplateRepository;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsQueryHandler;
use PrestaShop\PrestaShop\Core\Domain\MailTemplate\Query\GetEmailBodyTemplateForEditing;
use PrestaShop\PrestaShop\Core\Domain\MailTemplate\QueryHandler\GetEmailBodyTemplateForEditingHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\MailTemplate\QueryResult\EditableEmailBodyTemplate;

/**
 * @internal
 */
#[AsQueryHandler]
class GetEmailBodyTemplateForEditingHandler implements GetEmailBodyTemplateForEditingHandlerInterface
{
    public function __construct(
        private readonly EmailBodyTemplateRepository $repository,
    ) {
    }

    public function handle(GetEmailBodyTemplateForEditing $query): EditableEmailBodyTemplate
    {
        $data = $this->repository->findOne(
            $query->getTemplateName()->getValue(),
            $query->getLocale(),
            $query->getSource()
        );

        return new EditableEmailBodyTemplate(
            $query->getTemplateName()->getValue(),
            $query->getLocale(),
            $query->getSource()->getSource(),
            $query->getSource()->getModuleName(),
            $data['html_content'],
            $data['txt_content'],
        );
    }
}
