<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataHandler;

use PrestaShop\PrestaShop\Core\Cache\Clearer\CacheClearerInterface;
use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\Currency\Command\AddCurrencyCommand;
use PrestaShop\PrestaShop\Core\Domain\Currency\Command\AddUnofficialCurrencyCommand;
use PrestaShop\PrestaShop\Core\Domain\Currency\Command\EditCurrencyCommand;
use PrestaShop\PrestaShop\Core\Domain\Currency\Command\EditUnofficialCurrencyCommand;
use PrestaShop\PrestaShop\Core\Domain\Currency\ValueObject\CurrencyId;

/**
 * Class CurrencyFormDataHandler
 */
final class CurrencyFormDataHandler implements FormDataHandlerInterface
{
    /**
     * @var CommandBusInterface
     */
    private $commandBus;

    /**
     * @var CacheClearerInterface[]
     */
    private $cacheClearerCollection;

    /**
     * @param CommandBusInterface $commandBus
     * @param CacheClearerInterface[] $cacheClearerCollection
     */
    public function __construct(
        CommandBusInterface $commandBus,
        array $cacheClearerCollection
    ) {
        $this->commandBus = $commandBus;
        $this->cacheClearerCollection = $cacheClearerCollection;
    }

    /**
     * {@inheritdoc}
     */
    public function create(array $data)
    {
        if ($data['unofficial']) {
            $command = new AddUnofficialCurrencyCommand(
                $data['iso_code'],
                (float) $data['exchange_rate'],
                $data['active']
            );
        } else {
            $command = new AddCurrencyCommand(
                $data['iso_code'],
                (float) $data['exchange_rate'],
                $data['active']
            );
        }

        $command
            ->setPrecision((int) $data['precision'])
            ->setLocalizedNames($data['names'])
            ->setLocalizedSymbols($data['symbols'])
            ->setLocalizedTransformations($data['transformations'])
            ->setShopIds(is_array($data['shop_association']) ? $data['shop_association'] : [])
        ;

        /** @var CurrencyId $currencyId */
        $currencyId = $this->commandBus->handle($command);
        $this->clearCache();

        return $currencyId->getValue();
    }

    /**
     * {@inheritdoc}
     */
    public function update($id, array $data)
    {
        if ($data['unofficial']) {
            $command = new EditUnofficialCurrencyCommand((int) $id);
            $command
                ->setIsoCode($data['iso_code'])
            ;
        } else {
            $command = new EditCurrencyCommand((int) $id);
        }

        $command
            ->setLocalizedNames($data['names'])
            ->setLocalizedSymbols($data['symbols'])
            ->setLocalizedTransformations($data['transformations'])
            ->setExchangeRate((float) $data['exchange_rate'])
            ->setPrecision((int) $data['precision'])
            ->setIsEnabled($data['active'])
            ->setShopIds(is_array($data['shop_association']) ? $data['shop_association'] : [])
        ;

        $this->commandBus->handle($command);
        $this->clearCache();
    }

    /**
     * Clear the cache provided
     */
    private function clearCache()
    {
        foreach ($this->cacheClearerCollection as $cacheClearer) {
            $cacheClearer->clear();
        }
    }
}
