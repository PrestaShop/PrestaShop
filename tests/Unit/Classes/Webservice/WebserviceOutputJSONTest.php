<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Classes\Webservice;

use PHPUnit\Framework\TestCase;
use WebserviceOutputJSON;

class WebserviceOutputJSONTest extends TestCase
{
    /**
     * Listing the API root appends each resource name to the content as a plain string, while entities
     * are appended as arrays. Filtering the content used to assume every entry was an array, so asking
     * for the listing in JSON ended in "array_filter(): Argument #1 must be of type array, string given".
     *
     * This is the only test in this file on purpose: renderNodeHeader() keeps its "am I rendering the
     * API listing" flag in a static local, so a second test would inherit it and no longer describe a
     * fresh renderer.
     */
    public function testItRendersTheApiListingOfResourceNames(): void
    {
        $renderer = new WebserviceOutputJSON();
        $renderer->renderNodeHeader('api', []);
        $renderer->renderNodeHeader('customers', []);
        $renderer->renderNodeHeader('orders', []);

        $this->assertSame('["customers","orders"]', $renderer->overrideContent(''));
    }
}
