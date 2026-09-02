<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Twig\Component;

use PrestaShop\PrestaShop\Adapter\LegacyContext;
use PrestaShop\PrestaShop\Core\FeatureFlag\FeatureFlagStateCheckerInterface;
use PrestaShop\PrestaShop\Core\QuickAccess\QuickAccessGenerator;
use PrestaShopBundle\Security\Admin\UserTokenManager;
use PrestaShopBundle\Twig\Layout\MenuBuilder;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(template: '@PrestaShop/Admin/Component/Layout/quick_access.html.twig')]
class QuickAccess
{
    /**
     * List of Quick Accesses to display
     */
    protected ?array $quickAccesses = null;

    /**
     * Active Quick access based on current request uri
     */
    protected array|false|null $activeQuickAccess = null;

    /**
     * Clean current Url
     */
    protected ?string $currentPageQuickAccessLink = null;

    /**
     * Current page title
     */
    protected ?string $currentPageTitle = null;

    /**
     * Current page icon
     */
    protected ?string $currentPageIcon = null;

    public function __construct(
        protected readonly RequestStack $requestStack,
        protected readonly MenuBuilder $menuBuilder,
        protected readonly QuickAccessGenerator $quickAccessGenerator,
        protected readonly RouterInterface $router,
        protected readonly UserTokenManager $userTokenManager,
        protected readonly LegacyContext $legacyContext,
        protected readonly FeatureFlagStateCheckerInterface $featureFlagChecker,
    ) {
    }

    /**
     * Get quick accesses to display
     */
    public function getQuickAccesses(): array
    {
        if (null === $this->quickAccesses) {
            // Retrieve all quick accesses
            $quickAccesses = $this->quickAccessGenerator->getTokenizedQuickAccesses();

            // Prepare quick accesses to render the component view properly.
            foreach ($quickAccesses as &$quick) {
                // Verify if the link matches with the current page
                $cleanLink = $this->quickAccessGenerator->cleanQuickLink($quick['link']);
                $quick['active'] = $this->isCurrentPage($cleanLink);
            }
            $this->quickAccesses = $quickAccesses;
        }

        return $this->quickAccesses;
    }

    /**
     * Retrieve and prepare quick accesses data for twig view
     */
    public function getActiveQuickAccess(): array|false
    {
        if (null === $this->activeQuickAccess) {
            $this->activeQuickAccess = current(array_filter($this->getQuickAccesses(), fn ($data) => $data['active']));
        }

        return $this->activeQuickAccess;
    }

    /**
     * Get current clean url.
     */
    public function getCurrentPageQuickAccessLink(): string
    {
        if (null === $this->currentPageQuickAccessLink) {
            $request = $this->requestStack->getMainRequest();
            // We don't use $request->getUri() because it adds an unwanted / on urls that include index.php
            $uri = $request->getSchemeAndHttpHost() . $request->getRequestUri();
            $this->currentPageQuickAccessLink = $this->quickAccessGenerator->cleanQuickLink($uri);
        }

        return $this->currentPageQuickAccessLink;
    }

    /**
     * Get current title
     */
    public function getCurrentPageTitle(): string
    {
        if (null === $this->currentPageTitle) {
            $this->fillCurrentUrlFields();
        }

        return $this->currentPageTitle;
    }

    /**
     * Get current title
     */
    public function getCurrentPageIcon(): string
    {
        if (null === $this->currentPageIcon) {
            $this->fillCurrentUrlFields();
        }

        return $this->currentPageIcon;
    }

    public function getManageUrl(): string
    {
        if ($this->featureFlagChecker->isEnabled('quick_access')) {
            return $this->router->generate(
                'admin_quick_accesses_index',
                [],
                UrlGeneratorInterface::ABSOLUTE_URL
            );
        }

        return $this->legacyContext->getContext()->link->getAdminLink('AdminQuickAccesses');
    }

    public function getAjaxAddUrl(): string
    {
        if ($this->featureFlagChecker->isEnabled('quick_access')) {
            return $this->router->generate(
                'admin_quick_accesses_ajax_add',
                ['_token' => $this->userTokenManager->getSymfonyToken()],
                UrlGeneratorInterface::ABSOLUTE_URL
            );
        }

        return $this->legacyContext->getContext()->link->getAdminLink(
            'AdminQuickAccesses', true, [], ['action' => 'GetUrl', 'ajax' => 1]
        );
    }

    public function getAjaxDeleteUrl(): string
    {
        if ($this->featureFlagChecker->isEnabled('quick_access')) {
            return $this->router->generate(
                'admin_quick_accesses_ajax_delete',
                ['_token' => $this->userTokenManager->getSymfonyToken()],
                UrlGeneratorInterface::ABSOLUTE_URL
            );
        }

        return $this->legacyContext->getContext()->link->getAdminLink(
            'AdminQuickAccesses', true, [], ['action' => 'GetUrl', 'ajax' => 1]
        );
    }

    protected function fillCurrentUrlFields(): void
    {
        $breadcrumbLinks = $this->menuBuilder->getBreadcrumbLinks();
        if (isset($breadcrumbLinks['tab'])) {
            $this->currentPageTitle = $breadcrumbLinks['tab']->name;
            if (isset($breadcrumbLinks['action'])) {
                $this->currentPageTitle .= ' - ' . $breadcrumbLinks['action']->name;
            }
        } else {
            $this->currentPageTitle = '';
        }
        if (isset($breadcrumbLinks['container'])) {
            $this->currentPageIcon = $breadcrumbLinks['container']->icon ?? '';
        } else {
            $this->currentPageIcon = '';
        }
    }

    /**
     * Return true if the current page is the quick access url
     */
    protected function isCurrentPage(string $url): bool
    {
        // We don't compare the urls directly because they may have some small differences like ?addcartrule and ?addcartrule=
        // are different string but what matters is that the parameters value match
        $parsedUrl = parse_url($url);
        $parsedCurrentUrl = parse_url($this->getCurrentPageQuickAccessLink());

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
