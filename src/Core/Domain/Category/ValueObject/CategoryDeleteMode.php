<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Category\ValueObject;

use PrestaShop\PrestaShop\Core\Domain\Category\Exception\CategoryConstraintException;

/**
 * Class CategoryDeleteMode stores mode for category deletion.
 */
class CategoryDeleteMode
{
    /**
     * Associate products with parent category and disable them.
     */
    public const ASSOCIATE_PRODUCTS_WITH_PARENT_AND_DISABLE = 'associate_and_disable';

    /**
     * Associate products with parent and do not change their status.
     */
    public const ASSOCIATE_PRODUCTS_WITH_PARENT_ONLY = 'associate_only';

    /**
     * Remove products that are associated only with category that is being deleted.
     */
    public const REMOVE_ASSOCIATED_PRODUCTS = 'remove_associated';

    /**
     * @internal
     */
    public const AVAILABLE_MODES = [
        self::ASSOCIATE_PRODUCTS_WITH_PARENT_AND_DISABLE,
        self::ASSOCIATE_PRODUCTS_WITH_PARENT_ONLY,
        self::REMOVE_ASSOCIATED_PRODUCTS,
    ];

    /**
     * @var string
     */
    private $mode;

    /**
     * @param string $mode
     *
     * @throws CategoryConstraintException
     */
    public function __construct($mode)
    {
        $this->setMode($mode);
    }

    /**
     * @param string $mode
     *
     * @throws CategoryConstraintException
     */
    private function setMode($mode)
    {
        if (!in_array($mode, self::AVAILABLE_MODES)) {
            throw new CategoryConstraintException(sprintf('Invalid Category delete mode %s supplied. Available delete modes are: "%s"', var_export($mode, true), implode(',', self::AVAILABLE_MODES)), CategoryConstraintException::INVALID_DELETE_MODE);
        }

        $this->mode = $mode;
    }

    /**
     * Whether products associated with category should be removed.
     *
     * @return bool
     */
    public function shouldRemoveProducts()
    {
        return self::REMOVE_ASSOCIATED_PRODUCTS === $this->mode;
    }

    /**
     * Whether products should be disabled when category is deleted.
     *
     * @return bool
     */
    public function shouldDisableProducts()
    {
        return self::ASSOCIATE_PRODUCTS_WITH_PARENT_AND_DISABLE === $this->mode;
    }
}
