<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Module;

use PrestaShop\PrestaShop\Core\Module\DataProvider\PaymentModuleListProviderInterface;
use PrestaShop\PrestaShop\Core\Module\ModuleRepositoryInterface;
use PrestaShopBundle\Entity\Repository\ModuleRepository as ModuleEntityRepository;

/**
 * Class PaymentModuleListProvider is responsible for providing payment module list.
 */
final class PaymentModuleListProvider implements PaymentModuleListProviderInterface
{
    /**
     * @var ModuleRepositoryInterface
     */
    private $moduleRepository;

    /**
     * @var ModuleEntityRepository
     */
    private $moduleEntityRepository;

    /**
     * @var int
     */
    private $shopId;

    /**
     * @param ModuleRepositoryInterface $moduleRepository
     * @param ModuleEntityRepository $moduleEntityRepository
     * @param int $shopId
     */
    public function __construct(
        ModuleRepositoryInterface $moduleRepository,
        ModuleEntityRepository $moduleEntityRepository,
        int $shopId
    ) {
        $this->moduleRepository = $moduleRepository;
        $this->moduleEntityRepository = $moduleEntityRepository;
        $this->shopId = $shopId;
    }

    /**
     * {@inheritdoc}
     */
    public function getPaymentModuleList()
    {
        $modules = $this->moduleRepository->getInstalledModules();
        $paymentModules = [];

        /** @var Module $module */
        foreach ($modules as $module) {
            if ($module->attributes->get('is_paymentModule')) {
                $restrictedCountries = $this->moduleEntityRepository->findRestrictedCountryIds(
                    $module->database->get('id'),
                    $this->shopId
                );
                $restrictedCurrencies = $this->moduleEntityRepository->findRestrictedCurrencyIds(
                    $module->database->get('id'),
                    $this->shopId
                );
                $restrictedGroups = $this->moduleEntityRepository->findRestrictedGroupIds(
                    $module->database->get('id'),
                    $this->shopId
                );
                $restrictedCarriers = $this->moduleEntityRepository->findRestrictedCarrierReferenceIds(
                    $module->database->get('id'),
                    $this->shopId
                );

                $module->attributes->set('countries', $restrictedCountries);
                $module->attributes->set('currencies', $restrictedCurrencies);
                $module->attributes->set('groups', $restrictedGroups);
                $module->attributes->set('carriers', $restrictedCarriers);

                $paymentModules[$module->attributes->get('name')] = $module;
            }
        }

        return $paymentModules;
    }
}
