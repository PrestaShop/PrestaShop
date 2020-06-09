<?php
/**
 * 2007-2020 PrestaShop SA and Contributors
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
 * @copyright 2007-2020 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Util\Number;

use PrestaShop\Decimal\Number;
use Symfony\Component\PropertyAccess\Exception\AccessException;
use Symfony\Component\PropertyAccess\Exception\InvalidArgumentException;
use Symfony\Component\PropertyAccess\Exception\UnexpectedTypeException;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

/**
 * Extracts @var Number from given array/object
 */
class NumberExtractor
{
    /**
     * @var PropertyAccessorInterface
     */
    private $propertyAccessor;

    public function __construct(PropertyAccessorInterface $propertyAccessor)
    {
        $this->propertyAccessor = $propertyAccessor;
    }

    /**
     * @param $objectOrArray
     * @param string $propertyPath
     *
     * @return Number
     */
    public function extract($objectOrArray, string $propertyPath): Number
    {
        try {
            $plainValue = $this->propertyAccessor->getValue($objectOrArray, $propertyPath);
        } catch (InvalidArgumentException $e) {
            throw new NumberExtractorException(
                sprintf('Invalid property path "%s" provided', $propertyPath),
                NumberExtractorException::INVALID_PROPERTY_PATH,
                $e
            );
        } catch (AccessException $e) {
            throw new NumberExtractorException(
                sprintf(
                    'Cannot access property "%s". It doesn\'t exist or is not public',
                    $propertyPath
                ),
                NumberExtractorException::NOT_ACCESSIBLE,
                $e
            );
        } catch (UnexpectedTypeException $e) {
            throw new NumberExtractorException(
                'Invalid type of resource within a path given. Expected array or object',
                NumberExtractorException::INVALID_RESOURCE_TYPE,
                $e
            );
        }

        return $this->toNumber($plainValue);
    }

    /**
     * @param $value
     *
     * @return Number
     *
     * @throws NumberExtractorException
     */
    private function toNumber($value): Number
    {
        if (!is_numeric($value)) {
            throw new NumberExtractorException(
                sprintf(
                    'Only numeric values can be converted to Number. Got "%s"',
                    $value
                ),
                NumberExtractorException::NON_NUMERIC_PROPERTY
            );
        }

        return new Number((string) $value);
    }
}
