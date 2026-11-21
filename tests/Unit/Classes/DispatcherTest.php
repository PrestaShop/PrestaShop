<?php

/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Classes;

use Context;
use DispatcherCore;
use Language;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use Tools;

class DispatcherTest extends TestCase
{
    /**
     * @dataProvider createUrlProvider
     */
    public function testCreateUrl(string $routeName, string $rule, array $params, bool $useRoutes, string $expectedUrl): void
    {
        // Create an isolated dispatcher without loading shop configuration
        $reflection = new ReflectionClass(DispatcherCore::class);
        $dispatcher = $reflection->newInstanceWithoutConstructor();
        $property = $reflection->getProperty('use_routes');
        $property->setValue($dispatcher, $useRoutes);

        // Compile the supplied rule with product keywords, including for a module route
        $route = $dispatcher->computeRoute($rule, 'product', $dispatcher->default_routes['product_rule']['keywords']);
        $property = $reflection->getProperty('routes');
        $property->setValue($dispatcher, [1 => [1 => [$routeName => $route]]]);

        // Preserve the anchor after the path and any encoded query parameters
        $this->assertSame($expectedUrl . '#details', $dispatcher->createUrl($routeName, 1, $params, false, '#details', 1));
    }

    /**
     * Provide native and module routes with omitted keywords and extra parameters.
     */
    public static function createUrlProvider(): array
    {
        return [
            // Parameters absent from the active rule remain query parameters
            'omitted product ID' => [
                'product_rule', '{rewrite}', ['id' => 3, 'rewrite' => 'item'], true, 'item?id=3',
            ],
            'omitted null combination' => [
                'product_rule', '{rewrite}-p-{id}', ['id' => 3, 'rewrite' => 'item', 'id_product_attribute' => null], true, 'item-p-3',
            ],
            'omitted non-null combination' => [
                'product_rule', '{rewrite}-p-{id}', ['id' => 3, 'rewrite' => 'item', 'id_product_attribute' => 7], true, 'item-p-3?id_product_attribute=7',
            ],
            'omitted optional keyword' => [
                'product_rule', '{rewrite}-p-{id}', ['id' => 3, 'rewrite' => 'item', 'ean13' => '1234567890123'], true, 'item-p-3?ean13=1234567890123',
            ],

            // Classic URLs retain required keywords and additional parameters
            'classic URL' => [
                'product_rule', '{rewrite}-p-{id}', ['id' => 3, 'rewrite' => 'item', 'id_product_attribute' => 7, 'page' => 2], false,
                'index.php?id_product_attribute=7&page=2&rewrite=item&id_product=3&controller=product',
            ],

            // Encoding empty extra parameters must not append a question mark
            'null extra parameter' => [
                'product_rule', '{rewrite}-p-{id}', ['id' => 3, 'rewrite' => 'item', 'extra' => null], true, 'item-p-3',
            ],
            'empty array extra parameter' => [
                'product_rule', '{rewrite}-p-{id}', ['id' => 3, 'rewrite' => 'item', 'extra' => []], true, 'item-p-3',
            ],

            // Valid extra parameters, including zero, false and empty strings, retain their meaning
            'mixed extra parameters' => [
                'product_rule', '{rewrite}-p-{id}', ['id' => 3, 'rewrite' => 'item', 'extra' => null, 'page' => 0, 'flag' => false, 'search' => ''], true,
                'item-p-3?page=0&flag=0&search=',
            ],

            // A module route without a default definition keeps its additional parameters
            'module extra parameter' => [
                'module-custom-product', '{rewrite}-p-{id}', ['id' => 3, 'rewrite' => 'item', 'id_product_attribute' => 7], true,
                'item-p-3?id_product_attribute=7',
            ],
            'module null parameter' => [
                'module-custom-product', '{rewrite}-p-{id}', ['id' => 3, 'rewrite' => 'item', 'extra' => null], true, 'item-p-3',
            ],
        ];
    }

    /**
     * @dataProvider controllerRouteProvider
     */
    public function testGetControllerSelectsMostSpecificRoute(
        array $routes,
        string $uri,
        string $expectedController,
        array $expectedParameters,
        int $expectedFrontController = DispatcherCore::FC_FRONT,
    ): void {
        $context = Context::getContext();
        $query = $_GET;
        $post = $_POST;
        $toolsReflection = new ReflectionClass(Tools::class);
        $requestProperty = $toolsReflection->getProperty('request');
        $fallbackProperty = $toolsReflection->getProperty('fallbackParameters');
        $request = $requestProperty->getValue();
        $fallbackParameters = $fallbackProperty->getValue();

        try {
            $_GET = ['existing' => 'keep'];
            $_POST = [];
            Tools::resetRequest();
            Tools::setFallbackParameters([]);

            $testContext = new Context();
            $testContext->language = (new ReflectionClass(Language::class))->newInstanceWithoutConstructor();
            $testContext->language->id = 1;
            Context::setInstanceForTesting($testContext);

            $reflection = new ReflectionClass(DispatcherCore::class);
            $dispatcher = $reflection->newInstanceWithoutConstructor();
            $reflection->getProperty('use_routes')->setValue($dispatcher, true);
            $reflection->getProperty('request_uri')->setValue($dispatcher, $uri);
            $reflection->getProperty('empty_route')->setValue($dispatcher, null);

            foreach ($routes as $routeName => $route) {
                $dispatcher->addRoute(
                    $routeName,
                    $route['rule'],
                    $route['controller'],
                    1,
                    $route['keywords'],
                    $route['params'] ?? [],
                    1,
                );
            }

            $this->assertSame($expectedController, $dispatcher->getController(1));
            $this->assertSame($expectedFrontController, $reflection->getProperty('front_controller')->getValue($dispatcher));

            $expectedQuery = array_merge(['existing' => 'keep'], $expectedParameters, ['controller' => $expectedController]);
            $actualQuery = $_GET;
            ksort($expectedQuery);
            ksort($actualQuery);
            $this->assertSame($expectedQuery, $actualQuery);
        } finally {
            $_GET = $query;
            $_POST = $post;
            Context::setInstanceForTesting($context);
            $requestProperty->setValue(null, $request);
            $fallbackProperty->setValue(null, $fallbackParameters);
        }
    }

    public static function controllerRouteProvider(): array
    {
        $defaults = (new ReflectionClass(DispatcherCore::class))->getDefaultProperties()['default_routes'];
        $product = $defaults['product_rule'];
        $attachment = $defaults['attachment_rule'];
        $customProduct = $product;
        $customProduct['rule'] = '{category:/}{id}{-:id_product_attribute}-{rewrite}{-:ean13}';
        $issueRoutes = ['product_rule' => $customProduct, 'attachment_rule' => $attachment];

        $generic = [
            'controller' => 'module-losing-view',
            'rule' => 'files/{path}',
            'keywords' => ['path' => ['regexp' => '.+', 'param' => 'path']],
            'params' => ['fc' => 'module', 'source' => 'losing', 'losing_route' => 'leaked'],
        ];
        $customAttachment = $attachment;
        $customAttachment['rule'] = 'files/manuals/{id}-{rewrite}';
        $customAttachment['params'] = ['source' => 'selected'];
        $customRoutes = ['generic' => $generic, 'manual' => $customAttachment];

        $cms = $defaults['cms_rule'];
        $cms['rule'] = $attachment['rule'];
        $tiedRoutes = ['attachment_rule' => $attachment, 'cms_rule' => $cms];

        $staticRoute = ['controller' => 'contact', 'rule' => 'files/help', 'keywords' => []];
        $moduleRoute = $customAttachment;
        $moduleRoute['controller'] = 'module-catalog-download';

        return [
            'issue 43575: generic product registered first' => [
                $issueRoutes, '/attachment/42-guidepdf', 'attachment', ['id_attachment' => '42'],
            ],
            'issue 43575: attachment registered first' => [
                array_reverse($issueRoutes, true), '/attachment/42-guidepdf', 'attachment', ['id_attachment' => '42'],
            ],
            'longer static prefix replaces earlier matching route without leaking parameters' => [
                $customRoutes, '/files/manuals/42-guide', 'attachment', ['id_attachment' => '42', 'source' => 'selected'],
            ],
            'later generic route cannot overwrite selected parameters' => [
                array_reverse($customRoutes, true), '/files/manuals/42-guide', 'attachment', ['id_attachment' => '42', 'source' => 'selected'],
            ],
            'equal prefixes preserve attachment registration priority' => [
                $tiedRoutes, '/attachment/42-guidepdf', 'attachment', ['id_attachment' => '42'],
            ],
            'equal prefixes preserve CMS registration priority' => [
                array_reverse($tiedRoutes, true), '/attachment/42-guidepdf', 'cms', ['id_cms' => '42'],
            ],
            'fully static route takes precedence' => [
                ['generic' => $generic, 'contact' => $staticRoute], '/files/help', 'contact', [],
            ],
            'longer prefix without a matching regex is ignored' => [
                $customRoutes, '/files/other/42-guide', 'view',
                ['path' => 'other/42-guide', 'fc' => 'module', 'source' => 'losing', 'losing_route' => 'leaked', 'module' => 'losing'],
                DispatcherCore::FC_MODULE,
            ],
            'ordinary product with combination' => [
                ['product_rule' => $product, 'attachment_rule' => $attachment], '/42-7-shirt.html', 'product',
                ['id_product' => '42', 'id_product_attribute' => '7', 'rewrite' => 'shirt'],
            ],
            'ordinary product without combination' => [
                ['product_rule' => $product, 'attachment_rule' => $attachment], '/42-shirt.html', 'product',
                ['id_product' => '42', 'id_product_attribute' => '', 'rewrite' => 'shirt'],
            ],
            'custom product with category combination and EAN' => [
                $issueRoutes, '/clothes/42-7-shirt-1234567890123', 'product',
                ['id_product' => '42', 'id_product_attribute' => '7', 'rewrite' => 'shirt'],
            ],
            'custom product without category or combination' => [
                $issueRoutes, '/42-shirt', 'product',
                ['id_product' => '42', 'id_product_attribute' => '', 'rewrite' => 'shirt'],
            ],
            'native module route resolves captured controller' => [
                ['module' => $defaults['module']], '/module/catalog/view', 'view',
                ['module' => 'catalog', 'fc' => 'module'], DispatcherCore::FC_MODULE,
            ],
            'custom module route wins over generic route' => [
                ['generic' => $generic, 'module-catalog-download' => $moduleRoute], '/files/manuals/42-guide', 'download',
                ['id_attachment' => '42', 'source' => 'selected', 'module' => 'catalog', 'fc' => 'module'],
                DispatcherCore::FC_MODULE,
            ],
            'no matching route leaves query parameters untouched' => [
                $issueRoutes, '/unknown/path', 'pagenotfound', [],
            ],
        ];
    }

    /**
     * @dataProvider validateRouteProvider
     */
    public function testValidateRoute($routeId, $rule, $defaultRoutes, $expectedResult, $expectedErrors)
    {
        $dispatcher = DispatcherCore::getInstance();

        // Inject default_routes property
        $reflection = new ReflectionClass($dispatcher);
        $property = $reflection->getProperty('default_routes');
        $property->setValue($dispatcher, $defaultRoutes);

        $errors = [];
        $result = $dispatcher->validateRoute($routeId, $rule, $errors);

        $this->assertSame($expectedResult, $result);
        $this->assertEquals($expectedErrors, $errors);
    }

    public function validateRouteProvider()
    {
        return [
            // Valid route: all keywords present, none unknown
            [
                'category_rule',
                'category/{id}-{rewrite}',
                [
                    'category_rule' => [
                        'controller' => 'category',
                        'rule' => 'category/{id}-{rewrite}',
                        'keywords' => [
                            'id' => ['regexp' => '[0-9]+', 'param' => 'id_category'],
                            'rewrite' => ['regexp' => '[_a-zA-Z0-9-]*'],
                        ],
                    ],
                ],
                true,
                ['missing' => [], 'unknown' => []],
            ],
            // Missing keyword
            [
                'category_rule',
                'category/{rewrite}',
                [
                    'category_rule' => [
                        'controller' => 'category',
                        'rule' => 'category/{id}-{rewrite}',
                        'keywords' => [
                            'id' => ['regexp' => '[0-9]+', 'param' => 'id_category'],
                            'rewrite' => ['regexp' => '[_a-zA-Z0-9-]*'],
                        ],
                    ],
                ],
                false,
                ['missing' => ['id'], 'unknown' => []],
            ],
            // Unknown keyword
            [
                'category_rule',
                'category/{id}-{rewrite}-{foo}',
                [
                    'category_rule' => [
                        'controller' => 'category',
                        'rule' => 'category/{id}-{rewrite}',
                        'keywords' => [
                            'id' => ['regexp' => '[0-9]+', 'param' => 'id_category'],
                            'rewrite' => ['regexp' => '[_a-zA-Z0-9-]*'],
                        ],
                    ],
                ],
                false,
                ['missing' => [], 'unknown' => ['foo']],
            ],
            // Both missing and unknown
            [
                'category_rule',
                'category/{rewrite}-{foo}',
                [
                    'category_rule' => [
                        'controller' => 'category',
                        'rule' => 'category/{id}-{rewrite}',
                        'keywords' => [
                            'id' => ['regexp' => '[0-9]+', 'param' => 'id_category'],
                            'rewrite' => ['regexp' => '[_a-zA-Z0-9-]*'],
                        ],
                    ],
                ],
                false,
                ['missing' => ['id'], 'unknown' => ['foo']],
            ],
            // Route id not found
            [
                'not_existing_rule',
                'category/{id}-{rewrite}',
                [
                    'category_rule' => [
                        'controller' => 'category',
                        'rule' => 'category/{id}-{rewrite}',
                        'keywords' => [
                            'id' => ['regexp' => '[0-9]+', 'param' => 'id_category'],
                            'rewrite' => ['regexp' => '[_a-zA-Z0-9-]*'],
                        ],
                    ],
                ],
                false,
                ['missing' => [], 'unknown' => []],
            ],
        ];
    }
}
