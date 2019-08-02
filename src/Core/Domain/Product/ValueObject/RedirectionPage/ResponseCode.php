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

use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductConstraintException;
use Symfony\Component\HttpFoundation\Response;

/**
 * Response code for product unavailable case.
 */
class ResponseCode
{
    public const AVAILABLE_RESPONSE_CODES = [
        Response::HTTP_MOVED_PERMANENTLY,
        Response::HTTP_FOUND,
        Response::HTTP_NOT_FOUND,
    ];

    public function __construct(int $responseCode)
    {
        if (!in_array($responseCode, self::AVAILABLE_RESPONSE_CODES, true)) {
            throw new ProductConstraintException(
                sprintf(
                    'Invalid response code for redirection type %d given. Available codes are "%s"',
                    $responseCode,
                    implode(',', self::AVAILABLE_RESPONSE_CODES)
                ),
                ProductConstraintException::INVALID_RESPONSE_CODE
            );
        }

        $this->responseCode = $responseCode;
    }

    public function getValue(): int
    {
        return $this->responseCode;
    }
}
