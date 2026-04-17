<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\QuickAccess;

use PrestaShop\PrestaShop\Adapter\LegacyContext;
use PrestaShop\PrestaShop\Core\Context\EmployeeContext;
use PrestaShop\PrestaShop\Core\Context\LanguageContext;
use PrestaShop\PrestaShop\Core\Context\ShopContext;
use PrestaShop\PrestaShop\Core\Domain\Language\ValueObject\LanguageId;
use PrestaShopBundle\Entity\Employee\Employee;
use PrestaShopBundle\Entity\Repository\TabRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

/**
 * Generator that centralizes the generation/cleaning/fetching of quick accesses, so it can be used th same way in legacy
 * and symfony code.
 */
class QuickAccessGenerator
{
    /**
     * link to new product creation form
     */
    protected const NEW_PRODUCT_LINK = 'index.php/sell/catalog/products/new';

    /**
     * link to new product creation form for product v2
     */
    protected const NEW_PRODUCT_V2_LINK = 'index.php/sell/catalog/products/create';

    public function __construct(
        protected readonly LegacyContext $legacyContext,
        protected readonly LanguageContext $languageContext,
        protected readonly ShopContext $shopContext,
        protected readonly QuickAccessRepositoryInterface $quickAccessRepository,
        protected readonly TabRepository $tabRepository,
        protected readonly CsrfTokenManagerInterface $tokenManager,
        protected readonly EmployeeContext $employeeContext,
        private readonly Security $security,
    ) {
    }

    /**
     * Clean the saved quick link from base domain, index.ph and token to return its minimal form.
     *
     * @param string $savedUrl
     *
     * @return string
     */
    public function cleanQuickLink(string $savedUrl): string
    {
        $legacyEnvironment = stripos($savedUrl, 'controller');

        $patterns = [
            '#' . $this->legacyContext->getContext()->link->getBaseLink() . '#',
            '#' . basename(_PS_ADMIN_DIR_) . '/#',
            '/index.php/',
            '/_?token=[^&]+/',
        ];

        // If __PS_BASE_URI__ = '/', it destroys urls when is 'product/new' or 'modules/manage' (vhost for example)
        $baseUri = $this->shopContext->getBaseURI();
        if ('/' !== $baseUri) {
            $patterns[] = '#' . $baseUri . '#';
        }

        $url = preg_replace($patterns, '', $savedUrl);
        $url = trim($url, '?&/');

        return 'index.php' . (!empty($legacyEnvironment) ? '?' : '/') . $url;
    }

    public function getTokenizedQuickAccesses(): array
    {
        // Retrieve all quick accesses
        $quickAccesses = $this->quickAccessRepository->fetchAll(new LanguageId($this->languageContext->getId()));
        if (empty($quickAccesses)) {
            return [];
        }

        // Prepare quick accesses to render the component view properly.
        foreach ($quickAccesses as $index => &$quick) {
            // Initialise our Quick Access
            $quick['class'] = '';
            $cleanLink = $this->cleanQuickLink($quick['link']);

            // Special case for product link because it is bound to a modal, however all other links would deserve to be checked for permission
            if ($cleanLink === self::NEW_PRODUCT_LINK || $cleanLink === self::NEW_PRODUCT_V2_LINK) {
                $connectedUser = $this->security->getUser();
                if (!($connectedUser instanceof Employee) || !in_array('ROLE_MOD_TAB_ADMINPRODUCTS_CREATE', $connectedUser->getRoles())) {
                    // if employee has no access, we don't show product creation link,
                    // because it causes modal-related issues in product v2
                    unset($quickAccesses[$index]);
                    continue;
                }
                // We create new product v2 modal popup link
                $cleanLink = self::NEW_PRODUCT_V2_LINK;
                $quick['class'] = 'new-product-button';
            }

            // Preparation of the link to display in component view.
            $quick['link'] = $this->legacyContext->getContext()->link->getAdminBaseLink() . basename(_PS_ADMIN_DIR_) . '/' . $cleanLink;

            // Add token if needed
            $quick['link'] = $this->getTokenizedUrl($quick['link']);
        }

        return $quickAccesses;
    }

    /**
     * Get tokenized url
     */
    protected function getTokenizedUrl(string $baseUrl): string
    {
        $separator = strpos($baseUrl, '?') ? '&' : '?';

        $userIdentifier = $this->security->getUser()?->getUserIdentifier();
        if (!empty($userIdentifier) && !str_contains('_token', $baseUrl)) {
            $baseUrl .= $separator . '_token=' . $this->tokenManager->getToken($userIdentifier)->getValue();
        }

        return $baseUrl;
    }
}
