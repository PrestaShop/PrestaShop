<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\MailTemplate\QueryHandler;

use PrestaShop\PrestaShop\Core\Domain\MailTemplate\Query\GetEmailBodyTemplatesForListing;

interface GetEmailBodyTemplatesForListingHandlerInterface
{
    /**
     * Returns an array of email body templates for the given locale.
     *
     * @return array<int, array{template_name: string, source: string, module_name: string, has_html: bool, has_txt: bool}>
     */
    public function handle(GetEmailBodyTemplatesForListing $query): array;
}
