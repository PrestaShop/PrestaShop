<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace PrestaShopBundle\Twig\Component;

use PrestaShop\PrestaShop\Adapter\LegacyContext;
use PrestaShop\PrestaShop\Core\Domain\Language\ValueObject\LanguageId;
use PrestaShop\PrestaShop\Core\FeatureFlag\FeatureFlagSettings;
use PrestaShop\PrestaShop\Core\FeatureFlag\FeatureFlagStateCheckerInterface;
use PrestaShop\PrestaShop\Core\QuickAccess\QuickAccessRepositoryInterface;
use PrestaShop\PrestaShop\Core\Util\Url\UrlCleaner;
use PrestaShopBundle\Entity\Repository\TabRepository;
use PrestaShopBundle\Service\DataProvider\UserProvider;
use PrestaShopBundle\Twig\Layout\MenuBuilder;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(template: '@PrestaShop/Admin/Component/Layout/quick_access.html.twig')]
class QuickAccess
{
    /**
     * link to new product creation form
     */
    private const NEW_PRODUCT_LINK = 'index.php/sell/catalog/products/new';

    /**
     * link to new product creation form for product v2
     */
    private const NEW_PRODUCT_V2_LINK = 'index.php/sell/catalog/products-v2/create';

    /**
     * List of Quick Accesses to display
     */
    private array|null $quickAccesses = null;

    /**
     * Current Quick access by current request uri
     */
    private array|false|null $currentQuickAccess = null;

    /**
     * Clean current Url
     */
    private ?string $currentUrl = null;

    /**
     * Current url title
     */
    private ?string $currentUrlTitle = null;

    /**
     * Tokenized Urls cache
     */
    private array $tokenizedUrls = [];

    public function __construct(
        private readonly LegacyContext $context,
        private readonly QuickAccessRepositoryInterface $quickAccessRepository,
        private readonly TabRepository $tabRepository,
        private readonly CsrfTokenManagerInterface $tokenManager,
        private readonly UserProvider $userProvider,
        private readonly RequestStack $requestStack,
        private readonly FeatureFlagStateCheckerInterface $featureFlagStateChecker,
        private readonly MenuBuilder $menuBuilder
    ) {
    }

    /**
     * Get quick accesses to display
     */
    public function getQuickAccesses(): array
    {
        if (null === $this->quickAccesses) {
            // Get context
            $context = $this->context->getContext();

            // Get language and employee ids
            $languageId = new LanguageId($context->employee->id_lang);

            // Retrieve all quick accesses
            $quickAccesses = $this->quickAccessRepository->fetchAll($languageId);

            // Prepare quick accesses to render the component view properly.
            foreach ($quickAccesses as $index => &$quick) {
                // Initialise our Quick Access
                $quick['class'] = '';
                $quick['link'] = $context->link->getQuickLink($quick['link']);

                // If this quick access is legacy
                preg_match('/controller=(.+)(&.+)?$/', $quick['link'], $admin_tab);
                if (isset($admin_tab[1])) {
                    if (strpos($admin_tab[1], '&')) {
                        $admin_tab[1] = substr($admin_tab[1], 0, strpos($admin_tab[1], '&'));
                    }
                }
                // Let's build our url
                if ($quick['link'] === self::NEW_PRODUCT_LINK || $quick['link'] === self::NEW_PRODUCT_V2_LINK) {
                    if (!in_array('ROLE_MOD_TAB_ADMINPRODUCTS_CREATE', $this->userProvider->getUser()->getRoles())) {
                        // if employee has no access, we don't show product creation link,
                        // because it causes modal-related issues in product v2
                        unset($quickAccesses[$index]);
                        continue;
                    }
                    // if new product page feature is enabled we create new product v2 modal popup
                    if ($this->featureFlagStateChecker->isEnabled(FeatureFlagSettings::FEATURE_FLAG_PRODUCT_PAGE_V2)) {
                        $quick['link'] = self::NEW_PRODUCT_V2_LINK;
                        $quick['class'] = 'new-product-button';
                    }
                }

                // Preparation of the link to display in component view.
                $quick['link'] = '/' . basename(_PS_ADMIN_DIR_) . '/' . $quick['link'];

                // Verify if we are currently on this page.
                $quick['active'] = $this->isCurrentPage($quick['link']);

                // Add token if needed
                $quick['link'] = $this->getTokenizedUrl($quick['link']);
            }
            $this->quickAccesses = $quickAccesses;
        }

        return $this->quickAccesses;
    }

    /**
     * Retrieve and prepare quick accesses data for twig view
     */
    public function getCurrentQuickAccess(): array|false
    {
        if (null === $this->currentQuickAccess) {
            $this->currentQuickAccess = current(array_filter($this->getQuickAccesses(), fn ($data) => $data['active']));
        }

        return $this->currentQuickAccess;
    }

    /**
     * Get current clean url.
     */
    public function getCleanCurrentUrl(): string
    {
        if (null === $this->currentUrl) {
            $this->currentUrl = rtrim(UrlCleaner::cleanUrl($this->requestStack->getMainRequest()->getRequestUri(), ['_token', 'token']), '/');
        }

        return $this->currentUrl;
    }

    /**
     * Get current title
     */
    public function getCurrentUrlTitle(): string
    {
        if (null === $this->currentUrlTitle) {
            $breadcrumbLinks = $this->menuBuilder->getBreadcrumbLinks();
            if (isset($breadcrumbLinks['tab'])) {
                $this->currentUrlTitle = $breadcrumbLinks['tab']->name;
                if (isset($breadcrumbLinks['action'])) {
                    $this->currentUrlTitle .= ' - ' . $breadcrumbLinks['action']->name;
                }
            }
        }

        return $this->currentUrlTitle;
    }

    /**
     * Get url tokenized
     */
    private function getTokenizedUrl(string $baseUrl): string
    {
        if (!in_array($baseUrl, $this->tokenizedUrls)) {
            $url = $baseUrl;

            // Define separator and if the url is legacy or symfony.
            $separator = strpos($url, '?') ? '&' : '?';
            preg_match('/controller=(\w*)/', $url, $admin_tab);

            // If legacy link
            if (isset($admin_tab[1]) && !str_contains('token', $url)) {
                $token = $admin_tab[1] . $this->tabRepository->findOneIdByClassName($admin_tab[1]) . $this->context->getContext()->employee->id;
                $url .= $separator . 'token=' . \Tools::getAdminToken($token);
            }

            // If symfony link
            if (!isset($admin_tab[1]) && !str_contains('_token', $url)) {
                $url .= $separator . '_token=' . $this->tokenManager->getToken($this->userProvider->getUsername())->getValue();
            }
            $this->tokenizedUrls[$baseUrl] = $url;
        }

        return $this->tokenizedUrls[$baseUrl];
    }

    /**
     * Return true if the current page is the quick access url
     */
    private function isCurrentPage(string $url): bool
    {
        return 0 === strcasecmp($this->getCleanCurrentUrl(), rtrim($url, '/'));
    }
}
