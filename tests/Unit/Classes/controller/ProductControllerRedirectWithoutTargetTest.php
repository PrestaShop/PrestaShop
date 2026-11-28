<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Classes\Controller;

use Context;
use Link;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\RedirectType;
use Product;
use ProductControllerCore;
use RuntimeException;

/**
 * A disabled product carrying a redirect that names no target used to reach
 * Link::getProductLink(0) or Link::getCategoryLink(0), both of which throw, so an ordinary product page
 * answered 500 instead of 404.
 *
 * The state needs nothing exotic: the webservice accepts a redirect type without a target, and deleting
 * the product a redirect pointed at leaves one behind.
 */
class ProductControllerRedirectWithoutTargetTest extends TestCase
{
    /**
     * @dataProvider provideRedirectTypesWithoutTarget
     */
    public function testRedirectWithoutATargetAnswersNotFound(string $redirectType, int $defaultCategoryId): void
    {
        $controller = new RedirectTestProductController();
        $controller->setProductUnderTest($this->disabledProduct($redirectType, $defaultCategoryId));
        // With the recorder in place, any attempt to build a redirect link throws instead of returning,
        // so reaching the redirect branch at all is what fails the test rather than a missing stub.
        $controller->installLinkRecorder();

        $controller->checkPermissionsToViewProduct();

        $this->assertSame(
            RedirectType::TYPE_NOT_FOUND,
            $controller->getProductUnderTest()->redirect_type,
            sprintf('"%s" without a target should fall back to a 404.', $redirectType)
        );
        $this->assertContains('errors/404', $controller->templatesSet);
    }

    /**
     * @return iterable<string, array{string, int}>
     */
    public function provideRedirectTypesWithoutTarget(): iterable
    {
        yield '301-product with no target' => [RedirectType::TYPE_PRODUCT_PERMANENT, 0];
        yield '302-product with no target' => [RedirectType::TYPE_PRODUCT_TEMPORARY, 0];
        // The category branch fills the target from the default category, so it only breaks when that
        // is missing too - which is the second case the report describes.
        yield '301-category with no default category' => [RedirectType::TYPE_CATEGORY_PERMANENT, 0];
        yield '302-category with no default category' => [RedirectType::TYPE_CATEGORY_TEMPORARY, 0];
    }

    /**
     * A category redirect that does have a default category must still redirect, so the guard cannot be
     * a blanket "category redirects are 404". This is the control: it must keep its redirect type.
     */
    public function testCategoryRedirectStillUsesTheDefaultCategory(): void
    {
        $controller = new RedirectTestProductController();
        $controller->setProductUnderTest($this->disabledProduct(RedirectType::TYPE_CATEGORY_PERMANENT, 7));
        $controller->installLinkRecorder();

        // The redirect branch ends in exit(), so the recorder throws instead of returning a link. That
        // is what makes the redirect observable from a test at all.
        try {
            $controller->checkPermissionsToViewProduct();
            $this->fail('The category redirect was never performed.');
        } catch (RedirectLinkRequested $requested) {
            $this->assertSame('category', $requested->kind);
            $this->assertSame(7, $requested->targetId, 'The redirect did not target the default category.');
        }

        $this->assertSame(RedirectType::TYPE_CATEGORY_PERMANENT, $controller->getProductUnderTest()->redirect_type);
        $this->assertNotContains('errors/404', $controller->templatesSet);
    }

    private function disabledProduct(string $redirectType, int $defaultCategoryId): Product
    {
        $product = new RedirectTestProduct();
        $product->id = 42;
        $product->active = false;
        $product->redirect_type = $redirectType;
        $product->id_type_redirected = 0;
        $product->id_category_default = $defaultCategoryId;

        return $product;
    }
}

/**
 * Both methods this overrides query the database; nothing here needs their real answers.
 */
class RedirectTestProduct extends Product
{
    public function __construct()
    {
    }

    public function isAssociatedToShop($id_shop = null)
    {
        return true;
    }

    public function checkAccess($id_customer)
    {
        return true;
    }
}

/**
 * Renders nothing and translates nothing: the decision under test is which redirect type and which
 * template the controller settles on, not how either is produced.
 */
class RedirectTestProductController extends ProductControllerCore
{
    /**
     * @var string[]
     */
    public $templatesSet = [];

    public function __construct()
    {
    }

    public function setProductUnderTest(Product $product): void
    {
        $this->product = $product;
    }

    public function getProductUnderTest(): Product
    {
        return $this->product;
    }

    /**
     * Gives the controller a context whose link builder records the category it was asked for and
     * throws, so the redirect can be observed without running into the exit() that follows it.
     */
    public function installLinkRecorder(): void
    {
        $context = new Context();
        $context->link = new RecordingLink();

        $this->context = $context;
    }

    public function setTemplate($template, $params = [], $locale = null)
    {
        $this->templatesSet[] = $template;
    }

    protected function trans($id, array $parameters = [], $domain = null, $locale = null)
    {
        return $id;
    }
}

/**
 * Signals that the controller tried to build a redirect link, and for which target. In production that
 * call is what throws when the target is 0, which is how the 500 was produced.
 */
class RedirectLinkRequested extends RuntimeException
{
    /**
     * @var string
     */
    public $kind;

    /**
     * @var int
     */
    public $targetId;

    public function __construct(string $kind, int $targetId)
    {
        parent::__construct(sprintf('A %s redirect link was requested for id %d.', $kind, $targetId));
        $this->kind = $kind;
        $this->targetId = $targetId;
    }
}

/**
 * Records which category a link was requested for. It throws rather than returning, because the
 * controller calls exit() straight after building the redirect location.
 */
class RecordingLink extends Link
{
    public function __construct()
    {
    }

    public function getCategoryLink($category, $alias = null, $idLang = null, $selectedFilters = null, $idShop = null, $relativeProtocol = false)
    {
        throw new RedirectLinkRequested('category', (int) $category);
    }

    public function getProductLink($product, $alias = null, $category = null, $ean13 = null, $idLang = null, $idShop = null, $idProductAttribute = null, $force_routes = false, $relativeProtocol = false, $withIdInAnchor = false, $extraParams = [], bool $addAnchor = true)
    {
        throw new RedirectLinkRequested('product', (int) $product);
    }
}
