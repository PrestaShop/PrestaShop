<?php
/**
 * 2007-2018 PrestaShop
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

namespace Tests\Unit\Core\Grid\Query;

use stdClass;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Grid\Exception\UnsupportedParameterException;
use PrestaShop\PrestaShop\Core\Grid\Query\DoctrineQueryParser;

class DoctrineQueryParserTest extends TestCase
{
    /**
     * @var DoctrineQueryParser
     */
    private $queryParser;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        $this->queryParser = new DoctrineQueryParser();
    }

    public function testParseWithNamedParameters()
    {
        $preparedQuery = 'SELECT tests FROM pierre_rambaud WHERE motivation = :motivation AND energy = :energy';
        $queryParameters = [
            'motivation' => 'OK',
            'energy' => 'none',
        ];

        $expectedQuery = "SELECT tests FROM pierre_rambaud WHERE motivation = 'OK' AND energy = 'none'";

        $this->assertSame($expectedQuery, $this->queryParser->parse($preparedQuery, $queryParameters));
    }

    /**
     * @throws \PrestaShop\PrestaShop\Core\Grid\Exception\UnsupportedParameterException
     */
    public function testParseWithParametersMustThrowAnException()
    {
        $this->expectExceptionMessage('Only named parameters are supported in prepared queries.');
        $this->expectException(UnsupportedParameterException::class);

        $preparedQuery = 'SELECT tests FROM pierre_rambaud WHERE motivation = ? AND energy = ?';
        $queryParameters = ['OK', 'none'];

        $this->queryParser->parse($preparedQuery, $queryParameters);
    }

    public function testParseWithArrayNamedParameters()
    {
        $preparedQuery = 'SELECT tests FROM pierre_rambaud WHERE motivation IN (:motivation)';
        $queryParameters = [
            'motivation' => [
                'great',
                'good',
                'ok',
                'nok',
                'none',
            ]
        ];

        $expectedQuery = "SELECT tests FROM pierre_rambaud WHERE motivation IN ('great', 'good', 'ok', 'nok', 'none')";

        $this->assertSame($expectedQuery, $this->queryParser->parse($preparedQuery, $queryParameters));
    }

    /**
     * @throws \PrestaShop\PrestaShop\Core\Grid\Exception\UnsupportedParameterException
     */
    public function testParseWithArrayParametersMustThrowAnException()
    {
        $this->expectExceptionMessage('Only named parameters are supported in prepared queries.');
        $this->expectException(UnsupportedParameterException::class);

        $preparedQuery = 'SELECT tests FROM pierre_rambaud WHERE motivation IN (?)';
        $queryParameters = [
            [
                'great',
                'good',
                'ok',
                'nok',
                'none',
            ]
        ];

        $this->queryParser->parse($preparedQuery, $queryParameters);
    }

    public function testParseWithNullNamedParameters()
    {
        $preparedQuery = 'SELECT tests FROM pierre_rambaud WHERE motivation IS :motivation';
        $queryParameters = [
            'motivation' => null,
        ];

        $expectedQuery = "SELECT tests FROM pierre_rambaud WHERE motivation IS NULL";

        $this->assertSame($expectedQuery, $this->queryParser->parse($preparedQuery, $queryParameters));
    }

    public function testParseWithBooleanNamedParameters()
    {
        $preparedQuery = 'SELECT tests FROM pierre_rambaud WHERE motivation = :motivation';
        $queryParameters = [
            'motivation' => false,
        ];

        $expectedQuery = "SELECT tests FROM pierre_rambaud WHERE motivation = FALSE";

        $this->assertSame($expectedQuery, $this->queryParser->parse($preparedQuery, $queryParameters));

        $preparedQuery2 = 'SELECT tests FROM pierre_rambaud WHERE energy = :energy';
        $queryParameters2 = [
            'energy' => true,
        ];

        $expectedQuery2 = "SELECT tests FROM pierre_rambaud WHERE energy = TRUE";

        $this->assertSame($expectedQuery2, $this->queryParser->parse($preparedQuery2, $queryParameters2));
    }

    public function testParseWithUnsupportedTypeMustThrowAnException()
    {
        $this->expectExceptionMessage('Unsupported value type: object');
        $this->expectException(UnsupportedParameterException::class);

        $preparedQuery = 'SELECT tests FROM pierre_rambaud WHERE motivation IN (:motivation)';
        $queryParameters = [
            'motivation' => new stdClass(),
        ];

        $this->queryParser->parse($preparedQuery, $queryParameters);
    }

    public function testParseWithNumericNamedParameters()
    {
        $preparedQuery = 'SELECT * FROM product WHERE id_product = :id_product';
        $queryParameters = [
            'id_product' => 2,
        ];

        $expectedQuery = "SELECT * FROM product WHERE id_product = 2";

        $this->assertSame($expectedQuery, $this->queryParser->parse($preparedQuery, $queryParameters));

        $preparedQuery = 'SELECT * FROM product WHERE price = :price';
        $queryParameters = [
            'price' => 3.99,
        ];

        $expectedQuery = "SELECT * FROM product WHERE price = 3.99";

        $this->assertSame($expectedQuery, $this->queryParser->parse($preparedQuery, $queryParameters));
    }
}
