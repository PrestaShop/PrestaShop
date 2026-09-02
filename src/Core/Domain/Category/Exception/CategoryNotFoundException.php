<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Category\Exception;

use Exception;
use PrestaShop\PrestaShop\Core\Domain\Category\ValueObject\CategoryId;

/**
 * Class CategoryNotFoundException.
 */
class CategoryNotFoundException extends CategoryException
{
    /**
     * @var CategoryId
     */
    private $categoryId;

    /**
     * @param CategoryId $categoryId
     * @param string $message
     * @param int $code
     * @param Exception $previous
     */
    public function __construct(CategoryId $categoryId, $message = '', $code = 0, $previous = null)
    {
        parent::__construct($message, $code, $previous);

        $this->categoryId = $categoryId;
    }

    /**
     * @return CategoryId
     */
    public function getCategoryId()
    {
        return $this->categoryId;
    }
}
