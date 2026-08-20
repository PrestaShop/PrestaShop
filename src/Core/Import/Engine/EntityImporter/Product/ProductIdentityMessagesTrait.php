<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Import\Engine\EntityImporter\Product;

use PrestaShop\PrestaShop\Core\Import\Engine\ImportMessage;

/**
 * The two row-identity failures ProductFinder reports as data, worded once.
 *
 * They are raised TWICE by design — pre-emptively by ProductRowValidator in the
 * pausing validation phase, and again by ProductRowImporter as a database-phase
 * defense, since the catalog may have changed between the two. The wording has
 * to be identical in both places, which is exactly why it lives here instead of
 * being copied: only the phase id differs.
 *
 * The using class must expose a Symfony TranslatorInterface as
 * $this->translator.
 */
trait ProductIdentityMessagesTrait
{
    /**
     * The reference exists in the catalog but on none of the run's shops:
     * creating would duplicate it, updating is out of scope, so the row fails.
     */
    protected function referenceOutsideShopScopeMessage(string $reference, int $rowIndex, string $phaseId): ImportMessage
    {
        return new ImportMessage(
            ImportMessage::SEVERITY_ERROR,
            $phaseId,
            $this->translator->trans('The reference "%value%" matches a product outside the run\'s shop scope; the row was skipped to avoid creating a duplicate product.', ['%value%' => $reference], 'Admin.Advparameters.Notification'),
            $rowIndex,
            'reference'
        );
    }

    /**
     * The reference matches several in-scope products: updating an arbitrary one
     * of them is destructive and unrecoverable, so the row fails (an ambiguous
     * association LINK only warns — the blast radius differs).
     */
    protected function ambiguousReferenceMessage(string $reference, int $matchCount, int $rowIndex, string $phaseId): ImportMessage
    {
        return new ImportMessage(
            ImportMessage::SEVERITY_ERROR,
            $phaseId,
            $this->translator->trans('The reference "%value%" matches %count% products; the row was skipped to avoid updating the wrong one.', ['%value%' => $reference, '%count%' => $matchCount], 'Admin.Advparameters.Notification'),
            $rowIndex,
            'reference'
        );
    }
}
