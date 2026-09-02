<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\MailTemplate\QueryHandler;

use PrestaShop\PrestaShop\Adapter\MailTemplate\EmailBodyTemplateRepository;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsQueryHandler;
use PrestaShop\PrestaShop\Core\Domain\MailTemplate\Query\GetEmailBodyTemplatesForListing;
use PrestaShop\PrestaShop\Core\Domain\MailTemplate\QueryHandler\GetEmailBodyTemplatesForListingHandlerInterface;

/**
 * @internal
 */
#[AsQueryHandler]
class GetEmailBodyTemplatesForListingHandler implements GetEmailBodyTemplatesForListingHandlerInterface
{
    public function __construct(
        private readonly EmailBodyTemplateRepository $repository,
    ) {
    }

    public function handle(GetEmailBodyTemplatesForListing $query): array
    {
        return $this->repository->findAllForLocale($query->getLocale());
    }
}
