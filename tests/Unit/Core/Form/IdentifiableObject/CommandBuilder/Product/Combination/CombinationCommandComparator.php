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

namespace Tests\Unit\Core\Form\IdentifiableObject\CommandBuilder\Product\Combination;

use ReflectionClass;
use ReflectionException;
use SebastianBergmann\Comparator\Comparator;
use SebastianBergmann\Comparator\ComparisonFailure;

/**
 * The usual TestCase::assertEquals is not strict enough (null == false), but assertSame is too
 * strict on objects (only true when same reference is compared). So we introduce this custom
 * comparator that checks that:
 *  - both object are product commands (based on their namespace)
 *  - all their properties are strictly equal
 */
class CombinationCommandComparator extends Comparator
{
    /**
     * {@inheritdoc}
     */
    public function accepts($expected, $actual)
    {
        return $this->isCombinationCommand($expected) && $this->isCombinationCommand($actual);
    }

    /**
     * {@inheritdoc}
     */
    public function assertEquals($expected, $actual, $delta = 0.0, $canonicalize = false, $ignoreCase = false)
    {
        $reflection = new ReflectionClass($expected);
        foreach ($reflection->getProperties() as $reflectionProperty) {
            $reflectionProperty->setAccessible(true);
            $expectedProperty = $reflectionProperty->getValue($expected);
            $actualProperty = $reflectionProperty->getValue($actual);
            if (is_object($expectedProperty) && is_object($actualProperty)) {
                $this->assertEquals($expectedProperty, $actualProperty);
            } elseif ($expectedProperty !== $actualProperty) {
                throw new ComparisonFailure(
                    $expectedProperty,
                    $actualProperty,
                    $this->propertyToString($expectedProperty),
                    $this->propertyToString($actualProperty),
                    false,
                    sprintf(
                        'Invalid %s::%s expected %s but got %s instead',
                        get_class($expected),
                        $reflectionProperty->getName(),
                        $this->propertyToString($expectedProperty),
                        $this->propertyToString($actualProperty)
                    )
                );
            }
        }
    }

    /**
     * @param $property
     *
     * @return string
     */
    private function propertyToString($property): string
    {
        if (false === $property) {
            return 'false';
        }
        if (null === $property) {
            return 'null';
        }
        if (true === $property) {
            return 'true';
        }
        if (is_array($property)) {
            return json_encode($property);
        }

        return (string) $property;
    }

    /**
     * @param $command
     *
     * @return bool
     *
     * @throws ReflectionException
     */
    private function isCombinationCommand($command): bool
    {
        if (!is_object($command)) {
            return false;
        }

        $reflection = new ReflectionClass($command);

        return $reflection->getNamespaceName() === 'PrestaShop\PrestaShop\Core\Domain\Product\Combination\Command';
    }
}
