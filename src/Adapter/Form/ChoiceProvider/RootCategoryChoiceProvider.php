<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Form\ChoiceProvider;

use Category;
use PrestaShop\PrestaShop\Core\Context\LanguageContext;
use PrestaShop\PrestaShop\Core\Form\FormChoiceFormatter;
use PrestaShop\PrestaShop\Core\Form\FormChoiceProviderInterface;

/**
 * Provides the active root categories (the ones a shop can use) as name => id pairs.
 */
final class RootCategoryChoiceProvider implements FormChoiceProviderInterface
{
    public function __construct(
        private readonly LanguageContext $languageContext,
    ) {
    }

    public function getChoices(): array
    {
        return FormChoiceFormatter::formatFormChoices(Category::getRootCategories($this->languageContext->getId()), 'id_category', 'name');
    }
}
