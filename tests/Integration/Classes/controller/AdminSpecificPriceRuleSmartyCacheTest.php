<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Classes\controller;

use AdminSpecificPriceRuleControllerCore;
use Context;
use PHPUnit\Framework\TestCase;
use Smarty;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Opening Catalog Price Rules used to wipe the whole Smarty cache on every request. A rule that
 * changes must still drop it, since front office templates cache prices.
 *
 * @see https://github.com/PrestaShop/PrestaShop/issues/29298
 */
class AdminSpecificPriceRuleSmartyCacheTest extends TestCase
{
    /** @var Smarty|null */
    private $previousSmarty;

    /** @var string */
    private $workDir;

    /** @var string */
    private $cachedFile;

    protected function setUp(): void
    {
        parent::setUp();

        // A real Smarty of our own: a test earlier in the suite can leave a mocked one in the context.
        $context = Context::getContext();
        $this->previousSmarty = $context->smarty;
        $this->workDir = sys_get_temp_dir() . '/specific_price_rule_smarty_' . uniqid();
        $smarty = new Smarty();
        $smarty->setCacheDir($this->workDir . '/cache');
        $smarty->setCompileDir($this->workDir . '/compile');
        (new Filesystem())->mkdir([$smarty->getCacheDir(), $smarty->getCompileDir()]);
        $context->smarty = $smarty;

        $this->cachedFile = $smarty->getCacheDir() . 'specific_price_rule_test.php';
        file_put_contents($this->cachedFile, '<?php // cached fragment');
    }

    protected function tearDown(): void
    {
        Context::getContext()->smarty = $this->previousSmarty;
        (new Filesystem())->remove($this->workDir);
        parent::tearDown();
    }

    public function testListingKeepsTheSmartyCache(): void
    {
        $this->postProcess('');

        $this->assertFileExists($this->cachedFile);
    }

    public function testReadOnlyActionKeepsTheSmartyCache(): void
    {
        $this->postProcess('reset_filters');

        $this->assertFileExists($this->cachedFile);
    }

    public function testChangingActionClearsTheSmartyCache(): void
    {
        $this->postProcess('status');

        $this->assertFileDoesNotExist($this->cachedFile);
    }

    private function postProcess(string $action): void
    {
        (new TestSpecificPriceRuleController($action))->postProcess();
    }
}

class TestSpecificPriceRuleController extends AdminSpecificPriceRuleControllerCore
{
    public function __construct(string $action)
    {
        // what the constructor and AdminController::initProcess() set from the request before postProcess() runs
        $this->context = Context::getContext();
        $this->translator = $this->context->getTranslator();
        $this->action = $action;
    }
}
