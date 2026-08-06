<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\CartRule;

use CartRule;
use Db;
use PrestaShop\PrestaShop\Core\FeatureFlag\FeatureFlagSettings;
use PrestaShop\PrestaShop\Core\FeatureFlag\FeatureFlagStateCheckerInterface;
use PrestaShopCollection;

/**
 * Handles disabling of cart rules when their eligibility conditions are removed
 * (e.g. a customer or customer group is deleted).
 */
class CartRuleDisablerService
{
    public function __construct(
        private readonly FeatureFlagStateCheckerInterface $featureFlagStateChecker,
    ) {
    }

    /**
     * On customer deletion: disable cart rules that were restricted to this customer.
     * Resets the customer restriction so the discount is no longer tied to the deleted customer,
     * and disables it so the merchant can review and re-enable it manually.
     *
     * @param int $customerId
     */
    public function disableCartRulesThatHadCustomer(int $customerId): bool
    {
        if (!$this->featureFlagStateChecker->isEnabled(FeatureFlagSettings::FEATURE_FLAG_DISCOUNT)) {
            return true;
        }

        if (empty($customerId)) {
            return false;
        }

        $cartRules = new PrestaShopCollection('CartRule');
        $cartRules->where('id_customer', '=', $customerId);

        $result = true;
        foreach ($cartRules as $cartRule) {
            $cartRule->id_customer = 0;
            $cartRule->active = false;
            $result = $cartRule->update() && $result;
        }

        return $result;
    }

    /**
     * On group deletion: disable cart rules that had only this group as their restriction.
     * Clears the group restriction and disables the discount so the merchant can review it.
     * Must be called BEFORE the group rows are removed from the cart_rule_group table.
     *
     * @param int $groupId
     */
    public function disableCartRulesThatHadOnlyGroup(int $groupId): bool
    {
        if (!$this->featureFlagStateChecker->isEnabled(FeatureFlagSettings::FEATURE_FLAG_DISCOUNT)) {
            return true;
        }

        if (empty($groupId)) {
            return false;
        }

        $prefix = _DB_PREFIX_;
        $db = Db::getInstance();

        $cartRuleIds = $db->executeS(
            'SELECT crg.`id_cart_rule`
            FROM `' . $prefix . 'cart_rule_group` crg
            INNER JOIN `' . $prefix . 'cart_rule` cr ON cr.`id_cart_rule` = crg.`id_cart_rule` AND cr.`group_restriction` = 1
            WHERE crg.`id_group` = ' . (int) $groupId . '
            AND crg.`id_cart_rule` IN (
                SELECT `id_cart_rule` FROM `' . $prefix . 'cart_rule_group` GROUP BY `id_cart_rule` HAVING COUNT(*) = 1
            )'
        );

        if (empty($cartRuleIds)) {
            return true;
        }

        $result = true;
        foreach ($cartRuleIds as $row) {
            $cartRule = new CartRule((int) $row['id_cart_rule']);
            $cartRule->group_restriction = false;
            $cartRule->active = false;
            $result = $cartRule->update() && $result;
        }

        return $result;
    }

    /**
     * Disables all active free gift discounts that use the given product as their gift.
     * Called when a product becomes ineligible as a free gift (minimum quantity changed,
     * required customization added) or when the product is deleted.
     */
    public function disableCartRulesThatUsedProductAsGift(int $productId): void
    {
        if (!$this->featureFlagStateChecker->isEnabled(FeatureFlagSettings::FEATURE_FLAG_DISCOUNT)) {
            return;
        }

        $cartRules = new PrestaShopCollection(CartRule::class);
        $cartRules->where('gift_product', '=', $productId);
        $cartRules->where('active', '=', 1);

        foreach ($cartRules as $cartRule) {
            /* @var CartRule $cartRule */
            $cartRule->active = false;
            $cartRule->update();
        }
    }
}
