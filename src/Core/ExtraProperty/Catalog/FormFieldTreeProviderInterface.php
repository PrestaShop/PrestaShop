<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\ExtraProperty\Catalog;

/**
 * Provides the field tree of a back-office form, lazily built on demand (one form at a time,
 * since building every form eagerly would be far too expensive).
 */
interface FormFieldTreeProviderInterface
{
    /**
     * @param string $formId a form id known to the form catalog (form type block prefix)
     *
     * @return list<FormFieldNode>|null the root fields of the form, or null when the form id is
     *                                  unknown or the form cannot be built
     */
    public function getTree(string $formId): ?array;
}
