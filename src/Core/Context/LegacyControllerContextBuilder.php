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

namespace PrestaShop\PrestaShop\Core\Context;

use Doctrine\ORM\NoResultException;
use PrestaShop\PrestaShop\Adapter\ContextStateManager;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShop\PrestaShop\Core\Exception\InvalidArgumentException;
use PrestaShop\PrestaShop\Core\Util\Inflector;
use PrestaShopBundle\Entity\Repository\TabRepository;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Tools;

class LegacyControllerContextBuilder implements LegacyContextBuilderInterface
{
    private ?string $controllerName = null;
    private ?LegacyControllerContext $_legacyControllerContext = null;

    public function __construct(
        private readonly EmployeeContext $employeeContext,
        private readonly ContextStateManager $contextStateManager,
        private readonly array $controllersLockedToAllShopContext,
        private readonly TabRepository $tabRepository,
        private readonly ContainerInterface $container,
    ) {
    }

    public function build(): LegacyControllerContext
    {
        $this->assertArguments();

        $multiShopContext = $this->getMultiShopContext($this->controllerName);
        $id = $this->getTabId($this->controllerName);
        $employeeId = '';
        if ($this->employeeContext->getEmployee()) {
            $employeeId = $this->employeeContext->getEmployee()->getId();
        }
        $token = Tools::getAdminToken($this->controllerName . $id . $employeeId);
        $overrideFolder = Tools::toUnderscoreCase(substr($this->controllerName, 5)) . '/';
        $controllerType = 'admin';
        $className = $this->getClassName($this->controllerName);

        $legacyControllerContext = new LegacyControllerContext(
            $this->container,
            $this->controllerName,
            $controllerType,
            $this->controllerName,
            $multiShopContext,
            $className,
            $id,
            $token,
            $overrideFolder,
        );

        $this->_legacyControllerContext = $legacyControllerContext;

        return $legacyControllerContext;
    }

    public function buildLegacyContext(): void
    {
        $this->assertArguments();

        if (null === $this->_legacyControllerContext) {
            $this->_legacyControllerContext = $this->build();
        }

        $this->contextStateManager->setController($this->_legacyControllerContext);
    }

    public function setControllerName(string $controllerName): self
    {
        $this->controllerName = $controllerName;

        return $this;
    }

    private function assertArguments(): void
    {
        if (null === $this->controllerName) {
            throw new InvalidArgumentException(sprintf(
                'Cannot build Controller context as no controllerName has been defined you need to call %s::setControllerName to define it before building the Controller context',
                self::class
            ));
        }
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
            case 'AdminAccessController':
                return 'Profile';
            case 'AdminCarrierWizardController':
                return 'Carrier';
            case 'AdminImagesController':
                return 'ImageType';
            case 'AdminReturnController':
                return 'OrderReturn';
            case 'AdminSearchConfController':
                return 'Alias';
            case 'AdminConfigureFaviconBoController':
                return 'Configuration';
            default:
                // Here, we use the controller's name to retrieve the object model's name, passing it in singular form.
                if (preg_match('/Admin([a-zA-Z]+)Controller/', $controllerName, $matches)) {
                    return Inflector::getInflector()->singularize($matches[1]);
                } else {
                    return null;
                }
        }
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
}
