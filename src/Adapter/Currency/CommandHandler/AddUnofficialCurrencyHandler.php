<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Currency\CommandHandler;

use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Currency\CurrencyDataProviderInterface;
use PrestaShop\PrestaShop\Core\Domain\Currency\Command\AddUnofficialCurrencyCommand;
use PrestaShop\PrestaShop\Core\Domain\Currency\CommandHandler\AddUnofficialCurrencyHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Currency\Exception\CannotCreateCurrencyException;
use PrestaShop\PrestaShop\Core\Domain\Currency\Exception\CurrencyConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Currency\Exception\CurrencyException;
use PrestaShop\PrestaShop\Core\Domain\Currency\Exception\InvalidUnofficialCurrencyException;
use PrestaShop\PrestaShop\Core\Domain\Currency\ValueObject\CurrencyId;
use PrestaShop\PrestaShop\Core\Domain\Language\Exception\LanguageNotFoundException;
use PrestaShop\PrestaShop\Core\Language\LanguageInterface;
use PrestaShop\PrestaShop\Core\Localization\CLDR\LocaleRepository;
use PrestaShop\PrestaShop\Core\Localization\Currency\PatternTransformer;
use PrestaShop\PrestaShop\Core\Localization\Exception\LocalizationException;
use PrestaShopException;

/**
 * Adds a new unofficial currency.
 *
 * @internal
 */
#[AsCommandHandler]
final class AddUnofficialCurrencyHandler extends AbstractCurrencyHandler implements AddUnofficialCurrencyHandlerInterface
{
    /**
     * @var CurrencyDataProviderInterface
     */
    private $currencyDataProvider;

    /**
     * @param LocaleRepository $localeRepoCLDR
     * @param LanguageInterface[] $languages
     * @param CurrencyCommandValidator $validator
     * @param CurrencyDataProviderInterface $currencyDataProvider
     */
    public function __construct(
        LocaleRepository $localeRepoCLDR,
        array $languages,
        CurrencyCommandValidator $validator,
        CurrencyDataProviderInterface $currencyDataProvider,
        PatternTransformer $patternTransformer
    ) {
        parent::__construct($localeRepoCLDR, $languages, $validator, $patternTransformer);
        $this->currencyDataProvider = $currencyDataProvider;
    }

    /**
     * {@inheritdoc}
     *
     * @throws CannotCreateCurrencyException
     * @throws CurrencyException
     * @throws CurrencyConstraintException
     * @throws InvalidUnofficialCurrencyException
     * @throws LanguageNotFoundException
     * @throws LocalizationException
     */
    public function handle(AddUnofficialCurrencyCommand $command)
    {
        $this->validator->assertCurrencyIsNotInReference($command->getIsoCode()->getValue());
        $this->validator->assertCurrencyIsNotAvailableInDatabase($command->getIsoCode()->getValue());

        try {
            $entity = $this->currencyDataProvider->getCurrencyByIsoCodeOrCreate($command->getIsoCode()->getValue());

            $entity->unofficial = true;
            $entity->numeric_iso_code = null;
            if (null !== $command->getPrecision()) {
                $entity->precision = $command->getPrecision()->getValue();
            }

            $this->addEntity($entity, $command);
        } catch (PrestaShopException $exception) {
            throw new CurrencyException('Failed to create new currency', 0, $exception);
        }

        return new CurrencyId((int) $entity->id);
    }
}
