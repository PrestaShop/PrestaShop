<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Grid\Action\Row\AccessibilityChecker;

use Category;
use PrestaShop\PrestaShop\Core\Grid\Action\Row\AccessibilityChecker\AccessibilityCheckerInterface;

/**
 * Class CategoryForViewAccessibilityChecker.
 *
 * @internal
 */
final class CategoryForViewAccessibilityChecker implements AccessibilityCheckerInterface
{
    /**
     * @var int
     */
    private $contextLangId;

    /**
     * @param int $contextLangId
     */
    public function __construct($contextLangId)
    {
        $this->contextLangId = $contextLangId;
    }

    /**
     * {@inheritdoc}
     */
    public function isGranted(array $category)
    {
        return Category::hasChildren($category['id_category'], $this->contextLangId, false);
    }
}
