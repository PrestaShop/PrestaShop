<?php
/**
 * 2007-2019 PrestaShop and Contributors
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\RedirectionPage;

use PrestaShop\PrestaShop\Core\Domain\Category\Exception\CategoryException;
use PrestaShop\PrestaShop\Core\Domain\Category\ValueObject\CategoryId;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\RedirectionPage\TypedRedirectionPageInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\RedirectionPage\ResponseCode;

/**
 * Category redirection page
 */
final class CategoryRedirectionPage implements TypedRedirectionPageInterface
{
    /**
     * @var ResponseCode
     */
    private $responseCode;

    /**
     * @var CategoryId
     */
    private $categoryId;

    /**
     * @param int $responseCode
     * @param int $categoryId
     *
     * @throws ProductConstraintException
     * @throws CategoryException
     */
    public function __construct(int $responseCode, int $categoryId)
    {
        $this->responseCode = new ResponseCode($responseCode);
        $this->categoryId = new CategoryId($categoryId);
    }

    /**
     * {@inheritdoc}
     */
    public function getResponseCode(): ResponseCode
    {
        return $this->responseCode;
    }

    /**
     * {@inheritdoc}
     */
    public function getType(): string
    {
        return 'category';
    }

    /**
     * {@inheritdoc}
     */
    public function getId(): int
    {
        return $this->categoryId->getValue();
    }
}
