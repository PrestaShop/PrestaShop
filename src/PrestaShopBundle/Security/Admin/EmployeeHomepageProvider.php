<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Security\Admin;

use PrestaShop\PrestaShop\Adapter\LegacyContext;
use PrestaShopBundle\Entity\Employee\Employee;
use PrestaShopBundle\Entity\Repository\TabRepository;
use PrestaShopBundle\Entity\Tab;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Routing\RouterInterface;

/**
 * Service that generates the homepage url of the logged in employee based on their
 * custom configuration.
 */
class EmployeeHomepageProvider
{
    public function __construct(
        private readonly RouterInterface $router,
        private readonly Security $security,
        private readonly TabRepository $tabRepository,
        private readonly LegacyContext $legacyContext,
    ) {
    }

    public function getHomepageUrl(): ?string
    {
        $loggedUser = $this->security->getUser();
        if ($loggedUser instanceof Employee) {
            $homeUrl = null;
            if (!empty($loggedUser->getDefaultTabId())) {
                /** @var Tab|null $defaultTab */
                $defaultTab = $this->tabRepository->findOneBy(['id' => $loggedUser->getDefaultTabId()]);
                if (!empty($defaultTab)) {
                    if (!empty($defaultTab->getRouteName())) {
                        $homeUrl = $this->router->generate($defaultTab->getRouteName());
                    } elseif (!empty($defaultTab->getClassName())) {
                        $homeUrl = $this->legacyContext->getAdminLink($defaultTab->getClassName());
                    }
                }
            }

            if (null === $homeUrl) {
                $homeUrl = $this->legacyContext->getAdminLink('AdminDashboard');
            }

            return $homeUrl;
        }

        return null;
    }
}
