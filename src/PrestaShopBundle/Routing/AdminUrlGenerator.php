<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Routing;

use PrestaShop\PrestaShop\Core\Context\ShopContext;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Builds absolute back-office URLs without depending on the admin Router.
 *
 * The admin routing collection is only registered in the admin kernel, so
 * services triggered from other HTTP kernels cannot use $router->generate()
 * to build admin URLs.
 *
 * The Back Office base URL is built from the current request scheme and host
 * and the shop physical URI, without relying on the shop domain or virtual URI.
 */
class AdminUrlGenerator
{
    public function __construct(
        private readonly ShopContext $shopContext,
        private readonly string $adminFolderName,
        private readonly RequestStack $requestStack,
    ) {
    }

    public function generateAdminUrl(string $urlPath): string
    {
        $request = $this->requestStack->getCurrentRequest();

        // Password reset can also be triggered outside an HTTP request (e.g. Behat/Admin API),
        // so fall back to the shop context when no current request is available.
        if ($request !== null) {
            $baseUrl = rtrim($request->getSchemeAndHttpHost(), '/')
                . $this->shopContext->getPhysicalUri();
        } else {
            $baseUrl = $this->shopContext->getBaseURL();

            $baseUri = $this->shopContext->getBaseURI();

            if ($baseUri !== '') {
                $baseUrl = substr($baseUrl, 0, -strlen($baseUri))
                    . $this->shopContext->getPhysicalUri();
            }
        }

        return sprintf(
            '%s/%s/index.php%s',
            rtrim($baseUrl, '/'),
            trim($this->adminFolderName, '/'),
            $urlPath,
        );
    }
}
