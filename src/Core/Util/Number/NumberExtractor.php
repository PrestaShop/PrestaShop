<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Util\Number;

use PrestaShop\Decimal\DecimalNumber;
use ReflectionClass;
use ReflectionException;
use Symfony\Component\PropertyAccess\Exception\AccessException;
use Symfony\Component\PropertyAccess\Exception\InvalidArgumentException;
use Symfony\Component\PropertyAccess\Exception\UnexpectedTypeException;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

/**
 * Extracts numeric value as @var DecimalNumber from given resource
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
     * If provided resource is array, access its values using brackets e.g. '[one_property][another_level_property]'
     * If provided resource is object, access its properties using dots e.g. 'myProperty.anotherProperty'
     * You can also simply provide the name of property/key to reach the value if it is not multidimensional.
     *
     * object's public property will be extracted first,
     * else it will search for getters.
     * Note: this will only work when providing exact property name,
     * but not path selector for inner objects
     *
     * e.g:
     * ->extract($myMultiDimensionalArray, '[firstDimensionKey][secondDimensionKey]')
     *
     * ->extract($productForEditing, 'priceInformation.price')
     *
     * ->extract($productEntity, 'price')
     *
     * ->extract($simpleArray, '[someKey]')
     *
     * @param array|object $resource
     * @param string $propertyPath
     *
     * @return DecimalNumber
     *
     * @throws NumberExtractorException
     */
    public function extract($resource, string $propertyPath): DecimalNumber
    {
        if (is_object($resource)) {
            $numberFromPublicProperty = $this->extractPublicPropertyFirst($resource, $propertyPath);

            if (null !== $numberFromPublicProperty) {
                return $numberFromPublicProperty;
            }
        }

        try {
            $plainValue = $this->propertyAccessor->getValue($resource, $propertyPath);
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

        return $this->toDecimalNumber($plainValue);
    }

    /**
     * Check if object contains provided public property and extract it as a DecimalNumber, else return null
     *
     * @param string|object $resource
     * @param string $property
     *
     * @return DecimalNumber|null
     *
     * @throws ReflectionException
     */
    private function extractPublicPropertyFirst($resource, string $property): ?DecimalNumber
    {
        if (!property_exists($resource, $property)) {
            return null;
        }

        $reflectedObj = new ReflectionClass($resource);

        if (!$reflectedObj->getProperty($property)->isPublic()) {
            return null;
        }

        return $this->toDecimalNumber($resource->{$property});
    }

    /**
     * @param mixed $value
     *
     * @return DecimalNumber
     *
     * @throws NumberExtractorException
     */
    private function toDecimalNumber($value): DecimalNumber
    {
        if (!is_numeric($value)) {
            throw new NumberExtractorException(
                sprintf(
                    'Only numeric values can be converted to DecimalNumber. Got "%s"',
                    $value
                ),
                NumberExtractorException::NON_NUMERIC_PROPERTY
            );
        }

        return new DecimalNumber((string) $value);
    }
}
