<?php
/**
 * 2007-2018 PrestaShop.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Core\Domain\Category\ValueObject;

use PrestaShop\PrestaShop\Core\Domain\Category\Exception\CategoryConstraintException;

/**
 * Class DeleteMode stores mode for category deletion.
 */
class CategoryDeletionMode
{
    /**
     * Associate products with parent category and disable them.
     */
    const ASSOCIATE_PRODUCTS_WITH_PARENT_AND_DISABLE = 'associate_and_disable';

    /**
     * Associate products with parent and do not change their status.
     */
    const ASSOCIATE_PRODUCTS_WITH_PARENT_ONLY = 'associate_only';

    /**
     * Remove products that are associated only with category that is being deleted.
     */
    const REMOVE_ASSOCIATED_PRODUCTS = 'remove_associated';

    /**
     * @internal
     */
    const AVAILABLE_MODES = [
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
            throw new CategoryConstraintException(
                sprintf(
                    'Invalid Category delete mode %s supplied. Available delete modes are: "%s"',
                    var_export($mode, true),
                    implode(',', self::AVAILABLE_MODES)
                ),
                CategoryConstraintException::INVALID_DELETE_MODE
            );
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
