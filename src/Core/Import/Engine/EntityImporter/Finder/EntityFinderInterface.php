<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Import\Engine\EntityImporter\Finder;

use PrestaShop\PrestaShop\Core\Import\Engine\ImportRunContext;

/**
 * Contract of a MATCH-ONLY lookup: given one raw cell value, report which
 * entities it matches and nothing more. A finder NEVER creates anything, never
 * builds messages and never throws — severity and wording belong to the
 * calling importer, because the same ambiguity warns on an association link and
 * fails a row identity (see the ambiguity policy in the Import PLAN.md).
 *
 * Implementing this interface is what makes a finder reusable by the PR3
 * importers; ProductFinder deliberately stays outside it, because product
 * identity needs several distinct entry points (findRowMatch(),
 * findByReferenceThenId(), findTarget()) rather than one generic find().
 */
interface EntityFinderInterface
{
    public function find(string $value, ImportRunContext $context): FoundEntity;
}
