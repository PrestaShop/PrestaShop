<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Twig\Layout;

use PrestaShop\PrestaShop\Adapter\Feature\MultistoreFeature;
use PrestaShop\PrestaShop\Adapter\LegacyContext;
use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use PrestaShop\PrestaShop\Core\Context\EmployeeContext;
use PrestaShop\PrestaShop\Core\Context\LanguageContext;
use PrestaShop\PrestaShop\Core\Context\LegacyControllerContext;
use PrestaShop\PrestaShop\Core\Context\ShopContext;
use PrestaShopBundle\Entity\Repository\TabRepository;
use PrestaShopBundle\Entity\Tab;
use PrestaShopBundle\Security\Admin\UserTokenManager;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Allows you to construct variables used in rendering
 */
class TemplateVariablesBuilder
{
    public function __construct(
        private readonly LegacyContext $context,
        private readonly bool $debugMode,
        private readonly UserTokenManager $userTokenManager,
        private readonly string $psVersion,
        private readonly ConfigurationInterface $configuration,
        private readonly MenuBuilder $menuBuilder,
        private readonly TabRepository $tabRepository,
        private readonly EmployeeContext $employeeContext,
        private readonly LanguageContext $languageContext,
        private readonly ShopContext $shopContext,
        private readonly LegacyControllerContext $legacyControllerContext,
        private readonly MultistoreFeature $multistoreFeature,
        private readonly RequestStack $requestStack,
    ) {
    }

    public function build(): TemplateVariables
    {
        return new TemplateVariables(
            $this->languageContext->getIsoCode(),
            $this->languageContext->isRTL(),
            $this->legacyControllerContext->controller_name,
            $this->multistoreFeature->isActive(),
            $this->isMenuCollapsed(),
            $this->getJsRouterMetadata(),
            $this->debugMode,
            $this->installDirExists(),
            $this->psVersion,
            $this->getDefaultTabLink(),
            $this->isMaintenanceEnabled(),
            $this->isFrontOfficeAccessibleForAdmins(),
            $this->isDisplayedWithTabs(),
            $this->getBaseUrl(),
        );
    }

    private function isMenuCollapsed(): bool
    {
        $cookie = $this->context->getContext()->cookie;

        if (isset($cookie->collapse_menu)) {
            return boolval($cookie->collapse_menu);
        }

        return false;
    }

    private function getJsRouterMetadata(): array
    {
        return [
            // base url for javascript router
            'base_url' => $this->requestStack->getCurrentRequest()->getBaseUrl(),
            // security token for javascript router
            'token' => $this->userTokenManager->getSymfonyToken(),
        ];
    }

    private function installDirExists(): bool
    {
        return file_exists(_PS_ADMIN_DIR_ . '/../install');
    }

    private function getDefaultTabLink(): ?string
    {
        if ($this->employeeContext->getEmployee() && !empty($this->employeeContext->getEmployee()->getDefaultTabId())) {
            /** @var Tab|null $tab */
            $tab = $this->tabRepository->findOneBy(['id' => $this->employeeContext->getEmployee()->getDefaultTabId()]);

            if (!$tab) {
                $tab = $this->tabRepository->findOneByClassName('AdminDashboard');
            }

            return $this->context->getAdminLink($tab->getClassName());
        }

        return null;
    }

    private function isMaintenanceEnabled(): bool
    {
        return !(bool) $this->configuration->get('PS_SHOP_ENABLE');
    }

    private function isFrontOfficeAccessibleForAdmins(): bool
    {
        return (bool) $this->configuration->get('PS_MAINTENANCE_ALLOW_ADMINS');
    }

    private function isDisplayedWithTabs(): bool
    {
        return $this->menuBuilder->getCurrentTabLevel() >= 3;
    }

    private function getBaseUrl(): string
    {
        return $this->shopContext->getBaseURL();
    }
}
