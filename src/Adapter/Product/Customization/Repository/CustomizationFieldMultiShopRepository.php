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

namespace PrestaShop\PrestaShop\Adapter\Product\Customization\Repository;

use CustomizationField;
use Doctrine\DBAL\Connection;
use PrestaShop\PrestaShop\Adapter\Product\Customization\Validate\CustomizationFieldValidator;
use PrestaShop\PrestaShop\Core\Domain\Product\Customization\Exception\CannotAddCustomizationFieldException;
use PrestaShop\PrestaShop\Core\Domain\Product\Customization\Exception\CannotDeleteCustomizationFieldException;
use PrestaShop\PrestaShop\Core\Domain\Product\Customization\Exception\CannotUpdateCustomizationFieldException;
use PrestaShop\PrestaShop\Core\Domain\Product\Customization\Exception\CustomizationFieldNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Product\Customization\ValueObject\CustomizationFieldId;
use PrestaShop\PrestaShop\Core\Domain\Shop\Exception\InvalidShopConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopId;
use PrestaShop\PrestaShop\Core\Exception\CoreException;
use PrestaShop\PrestaShop\Core\Repository\AbstractMultiShopObjectModelRepository;

/**
 * Methods to access data storage for CustomizationField
 */
/**
 * @todo: This class has been added while we progressively migrate each domain to multishop It contains the new
 *        dedicated function bound with multishop When everything has been migrated they will be moved back to
 *        the initial CustomizationFieldRepository and single shop methods should be removed But since this will be done
 *        in several PRs for now it's easier to separate them into two services
 *        This is why a lot of code is duplicated between the two classes but don't worry this one is only temporary
 */
class CustomizationFieldMultiShopRepository extends AbstractMultiShopObjectModelRepository
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
     * @var CustomizationFieldValidator
     */
    private $customizationFieldValidator;

    /**
     * @param CustomizationFieldValidator $customizationFieldValidator
     * @param Connection $connection
     * @param string $dbPrefix
     */
    public function __construct(
        Connection $connection,
        string $dbPrefix,
        CustomizationFieldValidator $customizationFieldValidator
    ) {
        $this->connection = $connection;
        $this->dbPrefix = $dbPrefix;
        $this->customizationFieldValidator = $customizationFieldValidator;
    }

    /**
     * @param CustomizationFieldId $fieldId
     * @param ShopId $shopId
     *
     * @return CustomizationField
     *
     * @throws CoreException
     */
    public function get(CustomizationFieldId $fieldId, ShopId $shopId): CustomizationField
    {
        /** @var CustomizationField $customizationField */
        $customizationField = $this->getObjectModelForShop(
            $fieldId->getValue(),
            CustomizationField::class,
            CustomizationFieldNotFoundException::class,
            $shopId
        );

        return $customizationField;
    }

    /**
     * @param CustomizationField $customizationField
     * @param int[] $shopIds
     * @param int $errorCode
     *
     * @return CustomizationFieldId
     *
     * @throws CoreException
     */
    public function add(
        CustomizationField $customizationField,
        array $shopIds,
        int $errorCode = 0
    ): CustomizationFieldId {
        $this->customizationFieldValidator->validate($customizationField);
        $customizationField->id_shop_list = $shopIds;
        $this->addObjectModel($customizationField, CannotAddCustomizationFieldException::class, $errorCode);

        return new CustomizationFieldId((int) $customizationField->id);
    }

    /**
     * @param CustomizationField $customizationField
     * @param ShopConstraint $shopConstraint
     *
     * @throws CannotUpdateCustomizationFieldException
     */
    public function update(CustomizationField $customizationField, ShopConstraint $shopConstraint): void
    {
        $this->customizationFieldValidator->validate($customizationField);
        $shopIds = $this->getShopIdsByConstraint($customizationField, $shopConstraint);
        $this->updateObjectModelForShops(
            $customizationField,
            $shopIds,
            CannotUpdateCustomizationFieldException::class
        );
    }

    /**
     * @param CustomizationField $customizationField
     */
    public function delete(CustomizationField $customizationField): void
    {
        $this->deleteObjectModel($customizationField, CannotDeleteCustomizationFieldException::class);
    }

    /**
     * @param CustomizationField $customizationField
     */
    public function softDelete(CustomizationField $customizationField): void
    {
        $this->softDeleteObjectModel($customizationField, CannotDeleteCustomizationFieldException::class);
    }

    /**
     * @param CustomizationField $customizationField
     * @param ShopConstraint $shopConstraint
     *
     * @return int[]
     */
    private function getShopIdsByConstraint(CustomizationField $customizationField, ShopConstraint $shopConstraint): array
    {
        if ($shopConstraint->getShopGroupId()) {
            throw new InvalidShopConstraintException('Product has no features related with shop group use single shop and all shops constraints');
        }

        $shopIds = [];
        if ($shopConstraint->forAllShops()) {
            $shops = $this->getAssociatedShopIds($customizationField);
            foreach ($shops as $shopId) {
                $shopIds[] = $shopId->getValue();
            }
        } else {
            $shopIds = [$shopConstraint->getShopId()->getValue()];
        }

        return $shopIds;
    }

    /**
     * @param CustomizationField $customizationField
     *
     * @return ShopId[]
     */
    public function getAssociatedShopIds(CustomizationField $customizationField): array
    {
        $qb = $this->connection->createQueryBuilder();
        $qb
            ->select('id_shop')
            ->from($this->dbPrefix . 'product_shop', 'product_shop')
            ->leftJoin('product_shop', $this->dbPrefix . 'customization_field', 'custom', 'custom.id_product = product_shop.id_product')
            ->where('custom.id_product = :productId')
            ->setParameter('productId', $customizationField->id_product)
        ;

        $result = $qb->execute()->fetchAll();
        if (empty($result)) {
            return [];
        }

        $shops = [];
        foreach ($result as $shop) {
            $shops[] = new ShopId((int) $shop['id_shop']);
        }

        return $shops;
    }
}
