<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Language\QueryHandler;

use PrestaShop\PrestaShop\Core\Domain\Language\Query\GetLanguageForEditing;
use PrestaShop\PrestaShop\Core\Domain\Language\QueryResult\EditableLanguage;

/**
 * Interface for service that gets language for editing
 */
interface GetLanguageForEditingHandlerInterface
{
    /**
     * @param GetLanguageForEditing $query
     *
     * @return EditableLanguage
     */
    public function handle(GetLanguageForEditing $query);
}
