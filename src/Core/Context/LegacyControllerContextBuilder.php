<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Context;

use Doctrine\ORM\NoResultException;
use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShop\PrestaShop\Core\Util\Inflector;
use PrestaShopBundle\Entity\Repository\TabRepository;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Tools;
use Twig\Environment;

class LegacyControllerContextBuilder
{
    private ?string $controllerName = null;
    private ?string $redirectionUrl = null;

    public function __construct(
        protected readonly EmployeeContext $employeeContext,
        protected readonly array $controllersLockedToAllShopContext,
        protected readonly TabRepository $tabRepository,
        protected readonly ContainerInterface $container,
        protected readonly ConfigurationInterface $configuration,
        protected readonly RequestStack $requestStack,
        protected readonly ShopContext $shopContext,
        protected readonly LanguageContext $languageContext,
        protected readonly string $adminFolderName,
        protected string $psVersion,
        protected readonly Environment $twig,
    ) {
    }

    public function build(): LegacyControllerContext
    {
        $multiShopContext = $this->getMultiShopContext($this->getControllerName());
        $id = $this->getTabId($this->getControllerName());
        $employeeId = '';
        $employeeLanguageId = (int) $this->configuration->get('PS_LANG_DEFAULT');
        if ($this->employeeContext->getEmployee()) {
            $employeeId = $this->employeeContext->getEmployee()->getId();
            if ($this->configuration->get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG')) {
                $employeeLanguageId = $this->employeeContext->getEmployee()->getLanguageId();
            }
        }
        $token = Tools::getAdminToken($this->getControllerName() . $id . $employeeId);
        $overrideFolder = Tools::toUnderscoreCase(substr($this->getControllerName(), 5)) . '/';
        $controllerType = 'admin';
        $className = $this->getClassName($this->getControllerName());
        $table = $this->getTableFromClassName($this->getControllerName());

        return new LegacyControllerContext(
            $this->container,
            $this->getControllerName(),
            $controllerType,
            $multiShopContext,
            $className,
            $id,
            $token,
            $overrideFolder,
            $this->getCurrentIndex(),
            $table,
            $this->requestStack->getCurrentRequest() ?: Request::createFromGlobals(),
            $employeeLanguageId,
            $this->shopContext->getPhysicalUri(),
            $this->adminFolderName,
            $this->languageContext->isRTL(),
            $this->psVersion,
            $this->twig,
        );
    }

    public function setControllerName(string $controllerName): self
    {
        if (str_ends_with($controllerName, 'ControllerOverride')) {
            $controllerName = preg_replace('/ControllerOverride$/', '', $controllerName);
        }
        if (str_ends_with($controllerName, 'Controller')) {
            $controllerName = preg_replace('/Controller$/', '', $controllerName);
        }

        $this->controllerName = $controllerName;

        return $this;
    }

    public function setRedirectionUrl(?string $redirectionUrl): self
    {
        $this->redirectionUrl = $redirectionUrl;

        return $this;
    }

    private function getControllerName(): string
    {
        return $this->controllerName ?? 'AdminNotFound';
    }

    /**
     * This function is designed to locate the object model corresponding to the current Legacy Controller.
     *
     * @param string $controllerName
     *
     * @return string|null
     */
    private function getClassName(string $controllerName): ?string
    {
        switch ($controllerName) {
            case 'AdminAccess':
                return 'Profile';
            case 'AdminCarrierWizard':
                return 'Carrier';
            case 'AdminImages':
                return 'ImageType';
            case 'AdminReturn':
                return 'OrderReturn';
            case 'AdminSearchConf':
                return 'Alias';
            case 'AdminConfigureFaviconBo':
                return 'Configuration';
            default:
                // Here, we use the controller's name to retrieve the object model's name, passing it in singular form.
                if (preg_match('/Admin([a-zA-Z]+)/', $controllerName, $matches)) {
                    return Inflector::getInflector()->singularize($matches[1]);
                } else {
                    return null;
                }
        }
    }

    private function getTableFromClassName(?string $controllerName): string
    {
        // Handle special use cases that don't follow the usual rules
        switch ($controllerName) {
            case 'AdminAccess':
                return 'access';
        }

        $objectClassName = $this->getClassName($controllerName);
        if (empty($objectClassName) || !class_exists($objectClassName) || !property_exists($objectClassName, 'definition')) {
            return 'configuration';
        }

        $definition = $objectClassName::$definition;

        return $definition['table'] ?? 'configuration';
    }

    private function getTabId(string $controllerName): int
    {
        try {
            return $this->tabRepository->getIdByClassName($controllerName);
        } catch (NoResultException) {
            return -1;
        }
    }

    private function getMultiShopContext(string $controllerName): int
    {
        if (in_array($controllerName, $this->controllersLockedToAllShopContext)) {
            return ShopConstraint::ALL_SHOPS;
        } else {
            return ShopConstraint::ALL_SHOPS | ShopConstraint::SHOP_GROUP | ShopConstraint::SHOP;
        }
    }

    private function getCurrentIndex(): string
    {
        $parameters = [];
        if (!empty($this->controllerName)) {
            $parameters[] = 'controller=' . $this->controllerName;
        }
        if (!empty($this->redirectionUrl)) {
            $parameters[] = 'back=' . urlencode($this->redirectionUrl);
        }

        return 'index.php' . (!empty($parameters) ? '?' . implode('&', $parameters) : '');
    }
}
