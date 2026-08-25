<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Import\Engine\EntityImporter;

use PrestaShop\PrestaShop\Core\Import\Engine\Exception\ImportEngineException;
use PrestaShop\PrestaShop\Core\Import\Engine\ImportRunContext;
use PrestaShop\PrestaShop\Core\Language\LanguageInterface;

/**
 * Localized-value helpers shared by importer internals. The using class must
 * expose a Core LanguageRepositoryInterface as $this->languageRepository.
 *
 * Single-language-file rule: on entity CREATION a value is duplicated into
 * every installed language; on UPDATE only the file's language is written
 * (callers pick, see getLanguageId()).
 */
trait LocalizedValueTrait
{
    /**
     * @var list<int>|null memoized: language installs cannot happen mid-run
     */
    protected ?array $allLanguageIds = null;

    /**
     * @return array<int, string> the value duplicated into every installed language
     */
    protected function localizeForCreation(string $value): array
    {
        $localized = [];
        foreach ($this->getAllLanguageIds() as $languageId) {
            $localized[$languageId] = $value;
        }

        return $localized;
    }

    /**
     * @return list<int> every installed language id
     */
    protected function getAllLanguageIds(): array
    {
        return $this->allLanguageIds ??= array_map(
            static fn (LanguageInterface $language): int => $language->getId(),
            $this->languageRepository->findAll()
        );
    }

    /**
     * Id of the run's file language.
     *
     * @throws ImportEngineException when the iso code matches no installed language
     */
    protected function getLanguageId(ImportRunContext $context): int
    {
        $language = $this->languageRepository->getOneByIsoCode($context->getLangIso());
        if (null === $language) {
            throw new ImportEngineException(sprintf('Unknown language iso code "%s"', $context->getLangIso()));
        }

        return $language->getId();
    }
}
