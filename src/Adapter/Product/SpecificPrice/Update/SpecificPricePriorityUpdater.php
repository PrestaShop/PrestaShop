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

namespace PrestaShop\PrestaShop\Adapter\Product\SpecificPrice\Update;

use Doctrine\DBAL\Connection;
use Exception;
use PrestaShop\PrestaShop\Adapter\Configuration;
use PrestaShop\PrestaShop\Adapter\Shop\Context;
use PrestaShop\PrestaShop\Core\Domain\Product\SpecificPrice\Exception\CannotSetSpecificPricePrioritiesException;
use PrestaShop\PrestaShop\Core\Domain\Product\SpecificPrice\ValueObject\PriorityList;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShop\PrestaShop\Core\Exception\CoreException;
use PrestaShop\PrestaShop\Core\Feature\FeatureInterface;
use SpecificPrice;

/**
 * Responsible for updates related to specific price priorities
 */
class SpecificPricePriorityUpdater
{
    /**
     * @var Connection
     */
    private $connection;

    /**
     * @var string
     */
    private $dbPrefix;

    /**
     * @var Configuration
     */
    private $configuration;

    /**
     * @var Context
     */
    private $shopContext;

    /**
     * @var FeatureInterface
     */
    private $multistoreFeature;

    /**
     * SpecificPricePriorityUpdater constructor.
     *
     * @param Connection $connection
     * @param string $dbPrefix
     * @param Configuration $configuration
     * @param Context $shopContext
     * @param FeatureInterface $multistoreFeature
     */
    public function __construct(
        Connection $connection,
        string $dbPrefix,
        Configuration $configuration,
        Context $shopContext,
        FeatureInterface $multistoreFeature
    ) {
        $this->connection = $connection;
        $this->dbPrefix = $dbPrefix;
        $this->configuration = $configuration;
        $this->shopContext = $shopContext;
        $this->multistoreFeature = $multistoreFeature;
    }

    /**
     * @param ProductId $productId
     * @param PriorityList $priorityList
     *
     * @throws CoreException
     */
    public function setPrioritiesForProduct(ProductId $productId, PriorityList $priorityList): void
    {
        try {
            if (!SpecificPrice::setSpecificPriority($productId->getValue(), $priorityList->getPriorities())) {
                throw new CannotSetSpecificPricePrioritiesException(sprintf(
                    'Failed updating specific price priorities for product #%d',
                    $productId->getValue()
                ));
            }
        } catch (Exception $e) {
            throw new CoreException(sprintf(
                'Error occurred when trying to set specific price priorities for product #%d',
                $productId->getValue()
            ));
        }
    }

    /**
     * @param PriorityList $priorityList
     * @param ShopConstraint $shopConstraint
     * @param bool $isMultistoreChecked
     *
     * @throws CoreException
     */
    public function updateDefaultPriorities(PriorityList $priorityList, ShopConstraint $shopConstraint, bool $isMultistoreChecked): void
    {
        try {
            // If we are in multistore context (not all shop) and the multistore checkbox value is absent (it was unchecked),
            // then the field multistore value must be removed from DB for current context
            if ($this->multistoreFeature->isUsed() && !$this->shopContext->isAllShopContext() && !$isMultistoreChecked) {
                $this->configuration->deleteFromContext('PS_SPECIFIC_PRICE_PRIORITIES', $shopConstraint);
            } else {
                $this->configuration->set(
                    'PS_SPECIFIC_PRICE_PRIORITIES',
                    implode(';', $priorityList->getPriorities()),
                    $shopConstraint
                );
            }
        } catch (Exception $e) {
            throw new CoreException('Error occurred when trying to update default specific price priorities');
        }
    }

    /**
     * @param ProductId $productId
     */
    public function removePrioritiesForProduct(ProductId $productId): void
    {
        try {
            $this->connection->delete(
                $this->dbPrefix . 'specific_price_priority',
                ['id_product' => $productId->getValue()]
            );
        } catch (Exception $e) {
            throw new CoreException(sprintf(
                'Error occurred when trying to remove specific price priorities for product #%d',
                $productId->getValue()
            ));
        }
    }
}
