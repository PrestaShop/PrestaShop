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
class Cart extends CartCore
{
    /**
     * final constant, `final` declared before the visibility
     */
    final public const TEST_FINAL_CONSTANT = 'final';

    /**
     * private constant
     */
    private const TEST_PRIVATE_CONSTANT = 42;

    /**
     * static property with a nullable type
     */
    public static ?int $testNullableStaticProperty = null;

    /**
     * readonly property
     */
    protected readonly string $testReadonlyProperty;

    /**
     * property with a union type
     */
    public int|string $testUnionProperty = 1;

    /**
     * property typed with a class name
     */
    public ?DateTimeInterface $testFqcnProperty = null;

    /**
     * static property with an array type, returned by reference below
     */
    private static array $testReferenceStorage = [];

    /**
     * final static method
     */
    final public static function testFinalStaticMethod(): string
    {
        return 'final static';
    }

    /**
     * protected static method
     */
    protected static function testStaticMethod(): void
    {
        self::$testReferenceStorage = [];
    }

    /**
     * method returning by reference
     */
    public function &testReferenceMethod(): array
    {
        return self::$testReferenceStorage;
    }
}
