<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Currency\QueryHandler;

use Currency;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsQueryHandler;
use PrestaShop\PrestaShop\Core\Domain\Currency\Exception\CurrencyNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Currency\Query\GetCurrencyForEditing;
use PrestaShop\PrestaShop\Core\Domain\Currency\QueryHandler\GetCurrencyForEditingHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Currency\QueryResult\EditableCurrency;
use PrestaShop\PrestaShop\Core\Localization\Currency\PatternTransformer;

/**
 * Class GetCurrencyForEditingHandler is responsible for retrieving required data used in currency form.
 *
 * @internal
 */
#[AsQueryHandler]
final class GetCurrencyForEditingHandler implements GetCurrencyForEditingHandlerInterface
{
    /**
     * @var int
     */
    private $contextShopId;

    /**
     * @var PatternTransformer
     */
    private $patternTransformer;

    /**
     * @param int $contextShopId
     */
    public function __construct(
        int $contextShopId,
        PatternTransformer $patternTransformer
    ) {
        $this->contextShopId = $contextShopId;
        $this->patternTransformer = $patternTransformer;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(GetCurrencyForEditing $query)
    {
        $entity = new Currency(
            $query->getCurrencyId()->getValue(),
            null,
            $this->contextShopId
        );

        if (0 >= $entity->id) {
            throw new CurrencyNotFoundException(sprintf('Currency object with id "%s" was not found for editing', $query->getCurrencyId()->getValue()));
        }

        $transformations = [];
        foreach ($entity->getLocalizedPatterns() as $langId => $pattern) {
            $transformations[$langId] = !empty($pattern) ? $this->patternTransformer->getTransformationType($pattern) : '';
        }

        return new EditableCurrency(
            $entity->id,
            $entity->iso_code,
            $entity->getLocalizedNames(),
            $entity->getLocalizedSymbols(),
            $transformations,
            $entity->conversion_rate,
            $entity->precision,
            $entity->active,
            $entity->unofficial,
            $entity->getAssociatedShops()
        );
    }
}
