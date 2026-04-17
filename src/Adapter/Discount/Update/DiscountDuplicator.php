<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Discount\Update;

use CartRule;
use Doctrine\DBAL\ArrayParameterType;
use Doctrine\DBAL\Connection;
use Language;
use PrestaShop\PrestaShop\Adapter\Discount\Repository\DiscountRepository;
use PrestaShop\PrestaShop\Core\Domain\Discount\DiscountSettings;
use PrestaShop\PrestaShop\Core\Domain\Discount\Exception\CannotDuplicateDiscountException;
use PrestaShop\PrestaShop\Core\Domain\Discount\ValueObject\DiscountId;
use PrestaShop\PrestaShop\Core\Hook\HookDispatcherInterface;
use PrestaShop\PrestaShop\Core\Repository\AbstractObjectModelRepository;
use PrestaShop\PrestaShop\Core\Util\String\RandomString;
use PrestaShop\PrestaShop\Core\Util\String\StringModifierInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Duplicates discount
 */
class DiscountDuplicator extends AbstractObjectModelRepository
{
    public function __construct(
        protected readonly DiscountRepository $discountRepository,
        protected readonly HookDispatcherInterface $hookDispatcher,
        protected readonly Connection $connection,
        protected readonly string $dbPrefix,
        protected readonly TranslatorInterface $translator,
        protected readonly StringModifierInterface $stringModifier
    ) {
    }

    /**
     * Global process of duplicating a discount
     */
    public function duplicate(DiscountId $discountId): DiscountId
    {
        $oldDiscountId = $discountId->getValue();
        $this->hookDispatcher->dispatchWithParameters(
            'actionAdminDuplicateDiscountBefore',
            ['id_discount' => $oldDiscountId]
        );
        $newDiscountId = $this->duplicateDiscount($discountId);

        $this->duplicateRelations($discountId, $newDiscountId);

        $this->hookDispatcher->dispatchWithParameters(
            'actionAdminDuplicateDiscountAfter',
            ['id_discount' => $oldDiscountId, 'id_discount_new' => $newDiscountId->getValue()]
        );

        return $newDiscountId;
    }

    /**
     * Duplicates the discount itself
     */
    private function duplicateDiscount(DiscountId $sourceDiscountId): DiscountId
    {
        $cartRule = $this->discountRepository->get($sourceDiscountId);
        $duplicatedCartRule = $cartRule->duplicateObject();

        if (!$duplicatedCartRule instanceof CartRule) {
            throw new CannotDuplicateDiscountException('Failed to duplicate discount');
        }

        $duplicatedCartRule->name = $this->getNewProductName($cartRule->name);

        if ($duplicatedCartRule->code) {
            $duplicatedCartRule->code = RandomString::generateFromCharacters('123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ', 8);
        }

        $duplicatedCartRule->date_add = date('Y-m-d H:i:s');
        $duplicatedCartRule->active = false;
        $duplicatedCartRule->update();

        return new DiscountId((int) $duplicatedCartRule->id);
    }

    /**
     * Provides duplicated discount name
     *
     * @param array<int, string> $oldDiscountLocalizedNames
     *
     * @return array<int, string>
     */
    private function getNewProductName(array $oldDiscountLocalizedNames): array
    {
        $newLocalizedNames = [];
        foreach ($oldDiscountLocalizedNames as $langId => $oldName) {
            $langId = (int) $langId;
            $newName = $this->translator->trans('copy of %s', [$oldName], 'Admin.Catalog.Feature', Language::getLocaleById($langId));
            $newLocalizedNames[$langId] = $this->stringModifier->cutEnd($newName, DiscountSettings::MAX_NAME_LENGTH);
        }

        return $newLocalizedNames;
    }

    /**
     * Duplicates related discount associations
     */
    private function duplicateRelations(DiscountId $oldDiscountId, DiscountId $newDiscountId): void
    {
        $this->duplicateCountries($oldDiscountId, $newDiscountId);
        $this->duplicateCarriers($oldDiscountId, $newDiscountId);
        $this->duplicateCompatibleTypes($oldDiscountId, $newDiscountId);
        $this->duplicateGroups($oldDiscountId, $newDiscountId);
        $this->duplicateShops($oldDiscountId, $newDiscountId);
        $this->duplicateProductRules($oldDiscountId, $newDiscountId);
    }

    /**
     * Duplicates countries associated to a discount
     */
    private function duplicateCountries(DiscountId $oldDiscountId, DiscountId $newDiscountId): void
    {
        $countries = $this->discountRepository->getCountriesIds($oldDiscountId);

        foreach ($countries as $countryId) {
            $qb = $this->connection->createQueryBuilder();
            $qb->insert($this->dbPrefix . 'cart_rule_country')
                ->values([
                    'id_cart_rule' => ':id_discount',
                    'id_country' => ':id_country',
                ])
                ->setParameter('id_discount', $newDiscountId->getValue())
                ->setParameter('id_country', $countryId)
                ->executeQuery();
        }
    }

    /**
     * Duplicates carriers associated to a discount
     */
    private function duplicateCarriers(DiscountId $oldDiscountId, DiscountId $newDiscountId): void
    {
        $carriers = $this->discountRepository->getCarriersIds($oldDiscountId);

        foreach ($carriers as $carrierId) {
            $qb = $this->connection->createQueryBuilder();
            $qb->insert($this->dbPrefix . 'cart_rule_carrier')
                ->values([
                    'id_cart_rule' => ':id_discount',
                    'id_carrier' => ':id_carrier',
                ])
                ->setParameter('id_discount', $newDiscountId->getValue())
                ->setParameter('id_carrier', $carrierId)
                ->executeQuery();
        }
    }

    /**
     * Duplicates compatible types associated to a discount
     */
    private function duplicateCompatibleTypes(DiscountId $oldDiscountId, DiscountId $newDiscountId): void
    {
        $types = $this->discountRepository->getCompatibleTypesIds($oldDiscountId);

        foreach ($types as $typeId) {
            $qb = $this->connection->createQueryBuilder();
            $qb->insert($this->dbPrefix . 'cart_rule_compatible_types')
                ->values([
                    'id_cart_rule' => ':id_discount',
                    'id_cart_rule_type' => ':id_type',
                ])
                ->setParameter('id_discount', $newDiscountId->getValue())
                ->setParameter('id_type', $typeId)
                ->executeQuery();
        }
    }

    /**
     * Duplicates customer groups associated to a discount
     */
    private function duplicateGroups(DiscountId $oldDiscountId, DiscountId $newDiscountId): void
    {
        $groups = $this->discountRepository->getGroupsIds($oldDiscountId);

        foreach ($groups as $groupId) {
            $qb = $this->connection->createQueryBuilder();
            $qb->insert($this->dbPrefix . 'cart_rule_group')
                ->values([
                    'id_cart_rule' => ':id_discount',
                    'id_group' => ':id_group',
                ])
                ->setParameter('id_discount', $newDiscountId->getValue())
                ->setParameter('id_group', $groupId)
                ->executeQuery();
        }
    }

    /**
     * Duplicates shops associated to a discount
     */
    private function duplicateShops(DiscountId $oldDiscountId, DiscountId $newDiscountId): void
    {
        $shops = $this->discountRepository->getShopsIds($oldDiscountId);

        foreach ($shops as $shopId) {
            $qb = $this->connection->createQueryBuilder();
            $qb->insert($this->dbPrefix . 'cart_rule_shop')
                ->values([
                    'id_cart_rule' => ':id_discount',
                    'id_shop' => ':id_shop',
                ])
                ->setParameter('id_discount', $newDiscountId->getValue())
                ->setParameter('id_shop', $shopId)
                ->executeQuery();
        }
    }

    /**
     * Duplicates product rules associated to a discount
     */
    private function duplicateProductRules(DiscountId $oldDiscountId, DiscountId $newDiscountId): void
    {
        // First, we need to copy the rule groups (with their new IDs)
        $newRuleGroupsIds = [];
        $qb = $this->connection->createQueryBuilder();
        $ruleGroups = $qb
            ->select('*')
            ->from($this->dbPrefix . 'cart_rule_product_rule_group')
            ->where('id_cart_rule = :id_discount')
            ->setParameter('id_discount', $oldDiscountId->getValue())
            ->executeQuery()
            ->fetchAllAssociative();

        foreach ($ruleGroups as $ruleGroup) {
            $qb = $this->connection->createQueryBuilder();
            $qb->insert($this->dbPrefix . 'cart_rule_product_rule_group')
                ->values([
                    'id_cart_rule' => ':id_discount',
                    'quantity' => ':quantity',
                    'type' => ':type',
                ])
                ->setParameter('id_discount', $newDiscountId->getValue())
                ->setParameter('quantity', $ruleGroup['quantity'])
                ->setParameter('type', $ruleGroup['type'])
                ->executeQuery();
            $newRuleGroupsIds[$ruleGroup['id_product_rule_group']] = $this->connection->lastInsertId();
        }

        // Then, we can copy the rules, linking them to the new group IDs
        $newRulesIds = [];
        $qb = $this->connection->createQueryBuilder();
        $rules = $qb
            ->select('*')
            ->from($this->dbPrefix . 'cart_rule_product_rule')
            ->where('id_product_rule_group IN (:id_rule_groups)')
            ->setParameter('id_rule_groups', array_keys($newRuleGroupsIds), ArrayParameterType::INTEGER)
            ->executeQuery()
            ->fetchAllAssociative();

        foreach ($rules as $rule) {
            $qb = $this->connection->createQueryBuilder();
            $qb->insert($this->dbPrefix . 'cart_rule_product_rule')
                ->values([
                    'id_product_rule_group' => ':id_rule_group',
                    'type' => ':type',
                ])
                ->setParameter('id_rule_group', $newRuleGroupsIds[$rule['id_product_rule_group']])
                ->setParameter('type', $rule['type'])
                ->executeQuery();
            $newRulesIds[$rule['id_product_rule']] = $this->connection->lastInsertId();
        }

        // Finally, we can copy the rule values, linking them to the new rule IDs
        $qb = $this->connection->createQueryBuilder();
        $ruleValues = $qb
            ->select('*')
            ->from($this->dbPrefix . 'cart_rule_product_rule_value')
            ->where('id_product_rule IN (:id_rules)')
            ->setParameter('id_rules', array_keys($newRulesIds), ArrayParameterType::INTEGER)
            ->executeQuery()
            ->fetchAllAssociative();

        foreach ($ruleValues as $ruleValue) {
            $qb = $this->connection->createQueryBuilder();
            $qb->insert($this->dbPrefix . 'cart_rule_product_rule_value')
                ->values([
                    'id_product_rule' => ':id_rule',
                    'id_item' => ':id_item',
                ])
                ->setParameter('id_rule', $newRulesIds[$ruleValue['id_product_rule']])
                ->setParameter('id_item', $ruleValue['id_item'])
                ->executeQuery();
        }
    }
}
