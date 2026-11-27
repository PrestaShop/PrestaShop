<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Classes\controller;

use Context;
use FrontController;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class FrontControllerStructuredDataLogoTest extends KernelTestCase
{
    /** @var mixed */
    private $previousContainer;

    protected function setUp(): void
    {
        parent::setUp();
        self::bootKernel();
        $this->previousContainer = Context::getContext()->container ?? null;
        Context::getContext()->container = self::getContainer();
    }

    protected function tearDown(): void
    {
        Context::getContext()->container = $this->previousContainer;
        parent::tearDown();
    }

    public static function provideLogos(): iterable
    {
        yield 'a raster logo keeps its size' => [['src' => '/img/logo.png', 'width' => 240, 'height' => 80], ['width' => 240, 'height' => 80]];
        yield 'an SVG logo without an intrinsic size declares none' => [['src' => '/img/logo.svg', 'width' => 0, 'height' => 0], []];
        yield 'a logo without size keys declares none' => [['src' => '/img/logo.svg'], []];
    }

    /**
     * @param array<string, mixed> $logo
     * @param array<string, int> $expectedSize
     */
    #[DataProvider('provideLogos')]
    public function testTheOrganizationLogoOnlyDeclaresAKnownSize(array $logo, array $expectedSize): void
    {
        $structuredLogo = $this->makeController($logo)->getStructuredData()['organization']['logo'];

        $this->assertSame(
            array_merge(['@type' => 'ImageObject', 'url' => $logo['src']], $expectedSize),
            $structuredLogo
        );
    }

    /**
     * @param array<string, mixed> $logo
     */
    private function makeController(array $logo): FrontController
    {
        return new class($logo) extends FrontController {
            /** @var array<string, mixed> */
            private $logo;

            /**
             * @param array<string, mixed> $logo
             */
            public function __construct(array $logo)
            {
                $this->context = Context::getContext();
                $this->logo = $logo;
            }

            public function getShopLogo(): array
            {
                return $this->logo;
            }

            public function getTemplateVarUrls()
            {
                return ['pages' => ['index' => '/'], 'current_url' => '/'];
            }

            public function getTemplateVarPage()
            {
                return ['meta' => ['title' => 'Shop']];
            }

            protected function getBreadcrumbLinks()
            {
                return ['links' => []];
            }
        };
    }
}
