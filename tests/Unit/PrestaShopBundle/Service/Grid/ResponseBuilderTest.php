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

namespace Tests\Unit\PrestaShopBundle\Service\Grid;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Grid\Definition\Factory\GridDefinitionFactoryInterface;
use PrestaShop\PrestaShop\Core\Grid\Definition\GridDefinitionInterface;
use PrestaShop\PrestaShop\Core\Grid\Filter\GridFilterFormFactoryInterface;
use PrestaShopBundle\Entity\Repository\AdminFilterRepository;
use PrestaShopBundle\Service\Grid\ResponseBuilder;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Router;

class ResponseBuilderTest extends TestCase
{
    /**
     * @dataProvider dataProviderBuildSearchResponse
     *
     * @param array $data
     * @param array $redirectParams
     */
    public function testBuildSearchResponse(array $data, array $redirectParams): void
    {
        $mockForm = $this->createMock(FormInterface::class);
        $mockForm->method('isSubmitted')->willReturn(true);
        $mockForm->method('getData')->willReturn($data);

        $mockFilterFormFactory = $this->createMock(GridFilterFormFactoryInterface::class);
        $mockFilterFormFactory->method('create')->willReturn($mockForm);

        $mockDefinitionFactory = $this->createMock(GridDefinitionFactoryInterface::class);
        $mockDefinitionFactory->method('getDefinition')->willReturn(
            $this->createMock(GridDefinitionInterface::class)
        );

        $mockRouter = $this->createMock(Router::class);
        $mockRouter->method('generate')->willReturnCallback(function (string $name, array $parameters = []) {
            return $name . '?' . http_build_query($parameters);
        });

        $mockRequest = $this->createMock(Request::class);
        $mockRequest->setMethod('POST');
        $mockRequest->request = $this->createMock(ParameterBag::class);

        $responseBuilder = new ResponseBuilder(
            $mockFilterFormFactory,
            $mockRouter,
            $this->createMock(AdminFilterRepository::class),
            1,
            1
        );

        $response = $responseBuilder->buildSearchResponse(
            $mockDefinitionFactory,
            $mockRequest,
            'filterId',
            'index',
            []
        );

        self::assertInstanceOf(RedirectResponse::class, $response);

        $parts = parse_url($response->getTargetUrl());
        // Path
        self::assertEquals('index', $parts['path']);
        // Query
        parse_str($parts['query'] ?? '', $query);
        self::assertEquals($redirectParams, $query);
    }

    public function dataProviderBuildSearchResponse(): array
    {
        return [
            [
                [
                    'a' => 'b',
                ],
                [
                    'filterId' => [
                        'filters' => [
                            'a' => 'b',
                        ],
                    ],
                ],
            ],
            [
                [
                    'a' => null,
                ],
                [],
            ],
            [
                [
                    'a' => 'b',
                    'c' => null,
                ],
                [
                    'filterId' => [
                        'filters' => [
                            'a' => 'b',
                        ],
                    ],
                ],
            ],
            [
                [
                    'a' => [
                        'b' => 'c',
                    ],
                ],
                [
                    'filterId' => [
                        'filters' => [
                            'a' => [
                                'b' => 'c',
                            ],
                        ],
                    ],
                ],
            ],
            [
                [
                    'a' => [
                        'b' => null,
                    ],
                ],
                [],
            ],
            [
                [
                    'a' => [
                        'b' => 'c',
                        'd' => null,
                    ],
                ],
                [
                    'filterId' => [
                        'filters' => [
                            'a' => [
                                'b' => 'c',
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }
}
