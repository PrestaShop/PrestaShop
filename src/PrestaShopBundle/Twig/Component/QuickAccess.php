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
use PrestaShop\PrestaShop\Core\QuickAccess\QuickAccessRepositoryInterface;
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
    protected const NEW_PRODUCT_LINK = 'index.php/sell/catalog/products/new';

    /**
     * link to new product creation form for product v2
     */
    protected const NEW_PRODUCT_V2_LINK = 'index.php/sell/catalog/products/create';

    /**
     * List of Quick Accesses to display
     */
    protected array|null $quickAccesses = null;

    /**
     * Current Quick access by current request uri
     */
    protected array|false|null $currentQuickAccess = null;

    /**
     * Clean current Url
     */
    protected ?string $currentQuickAccessLink = null;

    /**
     * Current url title
     */
    protected ?string $currentUrlTitle = null;

    /**
     * Current url icon
     */
    protected ?string $currentUrlIcon = null;

    /**
     * Tokenized Urls cache
     */
    protected array $tokenizedUrls = [];

    public function __construct(
        protected readonly LegacyContext $context,
        protected readonly QuickAccessRepositoryInterface $quickAccessRepository,
        protected readonly TabRepository $tabRepository,
        protected readonly CsrfTokenManagerInterface $tokenManager,
        protected readonly UserProvider $userProvider,
        protected readonly RequestStack $requestStack,
        protected readonly MenuBuilder $menuBuilder
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

                // Verify if we are currently on this page before the link is modifed.
                $quick['active'] = $this->isCurrentPage($quick['link']);

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
                    // We create new product v2 modal popup link
                    $quick['link'] = self::NEW_PRODUCT_V2_LINK;
                    $quick['class'] = 'new-product-button';
                }

                // Preparation of the link to display in component view.
                $quick['link'] = '/' . basename(_PS_ADMIN_DIR_) . '/' . $quick['link'];

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
    public function getCurrentQuickAccessLink(): string
    {
        if (null === $this->currentQuickAccessLink) {
            $request = $this->requestStack->getMainRequest();
            // We don't use $request->getUri() because it adds an unwanted / on urls that include index.php
            $uri = $request->getSchemeAndHttpHost() . $request->getRequestUri();
            $this->currentQuickAccessLink = $this->context->getContext()->link->getQuickLink($uri);
        }

        return $this->currentQuickAccessLink;
    }

    /**
     * Get current title
     */
    public function getCurrentUrlTitle(): string
    {
        if (null === $this->currentUrlTitle) {
            $this->fillCurrentUrlFields();
        }

        return $this->currentUrlTitle;
    }

    /**
     * Get current title
     */
    public function getCurrentUrlIcon(): string
    {
        if (null === $this->currentUrlIcon) {
            $this->fillCurrentUrlFields();
        }

        return $this->currentUrlIcon;
    }

    protected function fillCurrentUrlFields(): void
    {
        $breadcrumbLinks = $this->menuBuilder->getBreadcrumbLinks();
        if (isset($breadcrumbLinks['tab'])) {
            $this->currentUrlTitle = $breadcrumbLinks['tab']->name;
            if (isset($breadcrumbLinks['action'])) {
                $this->currentUrlTitle .= ' - ' . $breadcrumbLinks['action']->name;
            }
        } else {
            $this->currentUrlTitle = '';
        }
        if (isset($breadcrumbLinks['container'])) {
            $this->currentUrlIcon = $breadcrumbLinks['container']->icon ?? '';
        } else {
            $this->currentUrlIcon = '';
        }
    }

    /**
     * Get url tokenized
     */
    protected function getTokenizedUrl(string $baseUrl): string
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
    protected function isCurrentPage(string $url): bool
    {
        // We don't compare the urls directly because they may have some small differences like ?addcartrule and ?addcartrule=
        // are different string but what matters is that the parameters value match
        $parsedUrl = parse_url($url);
        $parsedCurrentUrl = parse_url($this->getCurrentQuickAccessLink());

        $parsedUrlParameters = [];
        if (isset($parsedUrl['query'])) {
            parse_str($parsedUrl['query'], $parsedUrlParameters);
        }
        $parsedCurrentUrlParameters = [];
        if (isset($parsedCurrentUrl['query'])) {
            parse_str($parsedCurrentUrl['query'], $parsedCurrentUrlParameters);
        }

        return $parsedUrl['path'] === $parsedCurrentUrl['path'] && $parsedUrlParameters === $parsedCurrentUrlParameters;
    }
}
