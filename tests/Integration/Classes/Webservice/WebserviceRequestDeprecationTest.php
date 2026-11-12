<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Classes\Webservice;

use PHPUnit\Framework\TestCase;
use WebserviceRequest;

/**
 * The webservice error handler must not turn a PHP deprecation notice (e.g. the PHP 8.x
 * "Creation of dynamic property ..." notice) into a 500 error: doing so broke otherwise valid
 * POST/PUT API calls with "[PHP Unknown error #8192] ...".
 *
 * @see https://github.com/PrestaShop/PrestaShop/issues/39703
 */
class WebserviceRequestDeprecationTest extends TestCase
{
    public function testDeprecationNoticesAreNotReportedAsWebserviceErrors(): void
    {
        // getInstance() instantiates WebserviceRequest::$ws_current_classname, which is only set while
        // a real webservice request is being processed; initialise it for the test.
        if (empty(WebserviceRequest::$ws_current_classname)) {
            WebserviceRequest::$ws_current_classname = WebserviceRequest::class;
        }

        $request = WebserviceRequest::getInstance();
        $request->errors = [];

        // Force the handler down the escalation path it takes in production (display_errors off,
        // deprecations part of error_reporting) instead of the early "ignore" return.
        $previousDisplayErrors = ini_get('display_errors');
        $previousErrorReporting = error_reporting();
        ini_set('display_errors', 'off');
        error_reporting(E_ALL);

        try {
            $request->webserviceErrorHandler(E_DEPRECATED, 'Creation of dynamic property Foo::$bar is deprecated', __FILE__, __LINE__);
            $request->webserviceErrorHandler(E_USER_DEPRECATED, 'Some user deprecation', __FILE__, __LINE__);
        } finally {
            ini_set('display_errors', $previousDisplayErrors);
            error_reporting($previousErrorReporting);
        }

        $errors = $request->errors;
        $request->errors = [];

        $this->assertSame([], $errors, 'Deprecation notices must not be turned into webservice errors');
    }
}
