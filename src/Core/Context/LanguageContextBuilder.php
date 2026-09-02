<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Context;

use Language as LegacyLanguage;
use PrestaShop\PrestaShop\Adapter\ContextStateManager;
use PrestaShop\PrestaShop\Adapter\Language\Repository\LanguageRepository as ObjectModelLanguageRepository;
use PrestaShop\PrestaShop\Core\Domain\Language\ValueObject\LanguageId;
use PrestaShop\PrestaShop\Core\Exception\InvalidArgumentException;
use PrestaShop\PrestaShop\Core\Language\LanguageInterface;
use PrestaShop\PrestaShop\Core\Language\LanguageRepositoryInterface;
use PrestaShop\PrestaShop\Core\Localization\Locale\RepositoryInterface;
use PrestaShop\PrestaShop\Core\Localization\LocaleInterface;

class LanguageContextBuilder implements LegacyContextBuilderInterface
{
    use LegacyObjectCheckerTrait;

    private ?int $languageId = null;
    private ?int $defaultLanguageId = null;

    private ?LegacyLanguage $legacyLanguage = null;

    public function __construct(
        private readonly LanguageRepositoryInterface $languageRepository,
        private readonly RepositoryInterface $localeRepository,
        private readonly ContextStateManager $contextStateManager,
        private readonly ObjectModelLanguageRepository $objectModelLanguageRepository
    ) {
    }

    public function build(): LanguageContext
    {
        $this->assertArguments();

        /** @var LanguageInterface $language */
        $language = $this->languageRepository->find($this->languageId);

        return $this->buildLanguageContext($language);
    }

    public function buildDefault(): LanguageContext
    {
        $this->assertDefaultArguments();

        /** @var LanguageInterface $language */
        $language = $this->languageRepository->find($this->defaultLanguageId);

        return $this->buildLanguageContext($language);
    }

    public function setLanguageId(int $languageId): void
    {
        $this->languageId = $languageId;
    }

    public function setDefaultLanguageId(int $languageId): void
    {
        $this->defaultLanguageId = $languageId;
    }

    private function assertArguments(): void
    {
        if (null === $this->languageId) {
            throw new InvalidArgumentException(sprintf(
                'Cannot build Language context as no languageId has been defined you need to call %s::setLanguageId to define it before building the Language context',
                self::class
            ));
        }
    }

    private function assertDefaultArguments(): void
    {
        if (null === $this->defaultLanguageId) {
            throw new InvalidArgumentException(sprintf(
                'Cannot build Language context as no defaultLanguageId has been defined you need to call %s::setDefaultLanguageId to define it before building the Language context',
                self::class
            ));
        }
    }

    private function getLocaleByLanguage(LanguageInterface $language): LocaleInterface
    {
        return $this->localeRepository->getLocale($language->getLocale());
    }

    private function buildLanguageContext(LanguageInterface $language): LanguageContext
    {
        $localizationLocale = $this->getLocaleByLanguage($language);

        return new LanguageContext(
            id: (int) $language->getId(),
            name: $language->getName(),
            isoCode: $language->getIsoCode(),
            locale: $language->getLocale(),
            languageCode: $language->getLanguageCode(),
            isRTL: (bool) $language->isRTL(),
            dateFormat: $language->getDateFormat(),
            dateTimeFormat: $language->getDateTimeFormat(),
            localizationLocale: $localizationLocale,
        );
    }

    public function buildLegacyContext(): void
    {
        $this->assertArguments();

        $legacyLanguage = $this->getLegacyLanguage();
        $legacyLocale = $this->getLocaleByLanguage($legacyLanguage);

        // Only update the legacy context when the language is not the expected one, if not leave the context unchanged
        if ($this->legacyObjectNeedsUpdate($this->contextStateManager->getContext()->language, (int) $legacyLanguage->id)) {
            $this->contextStateManager->setLanguage($this->getLegacyLanguage());
        }

        /** @var LocaleInterface|null $contextLocale */
        $contextLocale = $this->contextStateManager->getContext()->currentLocale;
        // Only update the legacy context when the locale is not the expected one, if not leave the context unchanged
        if (empty($contextLocale) || $contextLocale->getCode() !== $legacyLocale->getCode()) {
            $this->contextStateManager->setCurrentLocale($legacyLocale);
        }
    }

    private function getLegacyLanguage(): ?LegacyLanguage
    {
        if ($this->legacyObjectNeedsUpdate($this->legacyLanguage, $this->languageId)) {
            $this->legacyLanguage = $this->objectModelLanguageRepository->get(new LanguageId($this->languageId));
        }

        return $this->legacyLanguage;
    }
}
