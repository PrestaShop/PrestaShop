<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\CartRule\QueryHandler;

use CartRule;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsQueryHandler;
use PrestaShop\PrestaShop\Core\Domain\CartRule\Query\SearchCartRules;
use PrestaShop\PrestaShop\Core\Domain\CartRule\QueryHandler\SearchCartRulesHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\CartRule\QueryResult\FoundCartRule;

/**
 * Searches for cart rules by search phrase using legacy object model
 */
#[AsQueryHandler]
final class SearchCartRulesHandler implements SearchCartRulesHandlerInterface
{
    /**
     * @var int
     */
    private $contextLangId;

    /**
     * @param int $contextLangId
     */
    public function __construct(int $contextLangId)
    {
        $this->contextLangId = $contextLangId;
    }

    /**
     * @param SearchCartRules $query
     *
     * @return FoundCartRule[]
     */
    public function handle(SearchCartRules $query): array
    {
        $searchPhrase = $query->getSearchPhrase();
        $foundCartRules = [];
        $cartRules = CartRule::getCartsRuleByCode($searchPhrase, $this->contextLangId, true);

        foreach ($cartRules as $cartRule) {
            $foundCartRules[] = new FoundCartRule(
                (int) $cartRule['id_cart_rule'],
                $cartRule['name'],
                $cartRule['code']
            );
        }

        return $foundCartRules;
    }
}
