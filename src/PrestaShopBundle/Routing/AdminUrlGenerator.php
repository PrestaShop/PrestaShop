<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Routing;

use Doctrine\ORM\EntityManagerInterface;
use PrestaShop\PrestaShop\Core\Context\ShopContext;
use PrestaShopBundle\Entity\ShopUrl;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Builds absolute back-office URLs without depending on the admin Router.
 *
 * The admin routing collection is only registered in the admin kernel, so
 * services triggered from other HTTP kernels cannot use $router->generate()
 * to build admin URLs.
 *
 * The request only selects an active configured shop URL by host, port and
 * physical URI. The resulting URL uses that configuration's domain and physical
 * URI, with the scheme provided by ShopContext. Virtual URIs are never included.
 * Without a matching URL or an HTTP request, the shop context is used instead.
 */
class AdminUrlGenerator
{
    public function __construct(
        private readonly ShopContext $shopContext,
        private readonly string $adminFolderName,
        private readonly RequestStack $requestStack,
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    public function generateAdminUrl(string $urlPath): string
    {
        $baseUrl = $this->shopContext->getBaseURL();
        $virtualUri = $this->shopContext->getVirtualUri();
        if ($virtualUri !== '') {
            $baseUrl = substr($baseUrl, 0, -strlen($virtualUri));
        }

        $request = $this->requestStack->getMainRequest();
        if ($request !== null) {
            $shopUrl = $this->findMatchingShopUrl($request);
            if ($shopUrl !== null) {
                // Keep the SSL policy of the context, including behind trusted proxies.
                $secure = str_starts_with($baseUrl, 'https://');
                $baseUrl = ($secure ? 'https://' . $shopUrl->getDomainSsl() : 'http://' . $shopUrl->getDomain())
                    . $shopUrl->getPhysicalUri();
            }
        }

        return sprintf(
            '%s/%s/index.php%s',
            rtrim($baseUrl, '/'),
            trim($this->adminFolderName, '/'),
            $urlPath,
        );
    }

    private function findMatchingShopUrl(Request $request): ?ShopUrl
    {
        $matchingUrl = null;
        $matchingLength = -1;
        $requestPath = $request->getBaseUrl() . $request->getPathInfo();

        // The standard repository also includes aliases, unlike the main shop URL provider.
        $shopUrls = $this->entityManager->getRepository(ShopUrl::class)->findBy(['active' => true], ['id' => 'ASC']);
        foreach ($shopUrls as $shopUrl) {
            if (!$shopUrl->getActive() || !$shopUrl->getShop()->getActive() || $shopUrl->getShop()->getDeleted()) {
                continue;
            }

            $physicalUri = '/' . trim($shopUrl->getPhysicalUri(), '/');
            if ($physicalUri !== '/') {
                $physicalUri .= '/';
            }

            if (!str_starts_with($requestPath, $physicalUri) || strlen($physicalUri) <= $matchingLength) {
                continue;
            }

            if (
                !$this->matchesAuthority($request, $shopUrl->getDomain())
                && !$this->matchesAuthority($request, $shopUrl->getDomainSsl())
            ) {
                continue;
            }

            $matchingUrl = $shopUrl;
            $matchingLength = strlen($physicalUri);
        }

        return $matchingUrl;
    }

    private function matchesAuthority(Request $request, string $domain): bool
    {
        if ($domain === '') {
            return false;
        }

        // Symfony normalizes host casing and default ports for both authorities.
        $configuredRequest = Request::create($request->getScheme() . '://' . $domain);

        return $request->getHost() === $configuredRequest->getHost()
            && $request->getPort() === $configuredRequest->getPort();
    }
}
