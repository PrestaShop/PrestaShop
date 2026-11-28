<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Category\Exception;

use PrestaShop\PrestaShop\Core\Domain\Category\ValueObject\CategoryId;
use Throwable;

/**
 * Is thrown when unable to delete category
 */
class FailedToDeleteCategoryException extends CategoryException
{
    /**
     * @var CategoryId|null
     */
    private $categoryId;

    /**
     * @param string $message
     * @param int $code
     * @param Throwable|null $previous
     * @param CategoryId|null $categoryId
     */
    public function __construct($message = '', $code = 0, $previous = null, ?CategoryId $categoryId = null)
    {
        parent::__construct($message, $code, $previous);

        $this->categoryId = $categoryId;
    }

    public function getCategoryId(): ?CategoryId
    {
        return $this->categoryId;
    }
}
