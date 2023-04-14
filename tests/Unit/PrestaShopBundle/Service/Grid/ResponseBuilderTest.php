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
use Symfony\Component\Form\FormConfigInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormErrorIterator;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\InputBag;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Router;

class ResponseBuilderTest extends TestCase
{
    private const ERROR_MESSAGE = 'An error occurred.';
    private const ERROR_LABEL = 'Field label';

    /**
     * @dataProvider dataProviderBuildSearchResponse
     *
     * @param array $data
     * @param array $redirectParams
     */
    public function testBuildSearchResponse(array $data, array $redirectParams): void
    {
        $response = $this->buildResponse($data, true);
        self::assertInstanceOf(RedirectResponse::class, $response);

        $parts = parse_url($response->getTargetUrl());
        // Path
        self::assertEquals('index', $parts['path']);
        // Query
        parse_str($parts['query'] ?? '', $query);
        self::assertEquals($redirectParams, $query);
    }

    /**
     * @dataProvider dataProviderBuildSearchResponse
     *
     * @param array $data
     * @param array $redirectParams
     */
    public function testInvalidBuildSearchResponse(array $data, array $redirectParams): void
    {
        // The error message are tested via the mocks
        $response = $this->buildResponse($data, false);
        self::assertInstanceOf(RedirectResponse::class, $response);

        $parts = parse_url($response->getTargetUrl());
        // Path
        self::assertEquals('index', $parts['path']);
        // Query
        parse_str($parts['query'] ?? '', $query);
        // The filters are ignored and not passed in redirect params when the form is invalid
        unset($redirectParams['filterId']);
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

    private function buildResponse(array $data, bool $isValid): RedirectResponse
    {
        $mockForm = $this->createMock(FormInterface::class);
        $mockForm->method('isSubmitted')->willReturn(true);
        $mockForm->method('isValid')->willReturn($isValid);
        $mockForm->method('getData')->willReturn($data);

        if (!$isValid) {
            $mockFieldConfig = $this->createMock(FormConfigInterface::class);
            $mockFieldConfig->method('getOption')->with('label')->willReturn(self::ERROR_LABEL);
            $mockFormField = $this->createMock(FormInterface::class);
            $mockFormField->method('getConfig')->willReturn($mockFieldConfig);

            $formError = new FormError(self::ERROR_MESSAGE);
            $formError->setOrigin($mockFormField);

            $mockForm->method('getErrors')->willReturn(new FormErrorIterator($mockForm, [$formError]));
        }

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
        $mockRequest->request = new InputBag();

        $session = $this->createMock(Session::class);
        if (!$isValid) {
            $mockFlashBag = $this->createMock(FlashBagInterface::class);
            $mockFlashBag->method('add')->with('error', sprintf('%s: %s', self::ERROR_LABEL, self::ERROR_MESSAGE));
            $session->method('getFlashBag')->willReturn($mockFlashBag);
        }

        $responseBuilder = new ResponseBuilder(
            $mockFilterFormFactory,
            $mockRouter,
            $this->createMock(AdminFilterRepository::class),
            1,
            1,
            $session
        );

        return $responseBuilder->buildSearchResponse(
            $mockDefinitionFactory,
            $mockRequest,
            'filterId',
            'index',
            []
        );
    }
}
