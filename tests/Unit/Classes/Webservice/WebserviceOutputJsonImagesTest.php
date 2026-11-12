<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Classes\Webservice;

use PHPUnit\Framework\TestCase;
use ReflectionProperty;
use WebserviceOutputJSONCore;

/**
 * The images resource renders its own nodes and passes no parameters, so the JSON renderer used to
 * index objectsNodeName on an empty array: a PHP notice, and every entry filed under an empty key.
 */
class WebserviceOutputJsonImagesTest extends TestCase
{
    public function testTheImagesAreFiledUnderTheCollectionThatWasOpened(): void
    {
        $renderer = new WebserviceOutputJSONCore();

        // What WebserviceSpecificManagementImages does: open the collection, then one node per image.
        $renderer->renderNodeHeader('images', []);
        $renderer->renderNodeHeader('image', [], ['id' => 221], false);
        $renderer->renderNodeHeader('image', [], ['id' => 711], false);

        $content = $this->contentOf($renderer);

        $this->assertArrayNotHasKey('', $content, 'nothing may be filed under an empty key');
        $this->assertSame(
            [['id' => 221], ['id' => 711]],
            $content['images'] ?? null
        );
    }

    public function testAConfiguredResourceStillUsesItsOwnCollectionName(): void
    {
        $renderer = new WebserviceOutputJSONCore();

        $renderer->renderNodeHeader('images', []);
        $renderer->renderNodeHeader('product', ['objectsNodeName' => 'products'], ['id' => 1], false);

        $content = $this->contentOf($renderer);

        $this->assertSame([['id' => 1]], $content['products'] ?? null, 'the parameters still win');
        $this->assertArrayNotHasKey('images', $content);
    }

    private function contentOf(WebserviceOutputJSONCore $renderer): array
    {
        $content = new ReflectionProperty(WebserviceOutputJSONCore::class, 'content');
        $content->setAccessible(true);

        return $content->getValue($renderer);
    }
}
