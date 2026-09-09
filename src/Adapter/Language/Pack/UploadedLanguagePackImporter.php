<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Language\Pack;

use Language;
use PrestaShop\PrestaShop\Core\Language\Pack\Import\Exception\InvalidTranslationPackException;
use PrestaShop\PrestaShop\Core\Language\Pack\Import\TranslationPackValidator;
use PrestaShop\PrestaShop\Core\Language\Pack\Import\UploadedLanguagePackImporterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Installs a translation pack supplied by the merchant, reusing the installation step that a
 * downloaded pack goes through so that both end up in the same state.
 */
class UploadedLanguagePackImporter implements UploadedLanguagePackImporterInterface
{
    public function __construct(
        private readonly TranslationPackValidator $validator,
        private readonly TranslatorInterface $translator
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function import(string $archivePath): array
    {
        try {
            $locale = $this->validator->validate($archivePath);
        } catch (InvalidTranslationPackException $exception) {
            return [$this->describe($exception)];
        }

        // The pack only carries catalogues; the language itself, its currency and its formats come
        // from adding the language, so importing into a locale the shop does not have would leave
        // files nothing reads.
        if (!Language::getIdByLocale($locale, true)) {
            return [
                $this->translator->trans(
                    'The language %locale% must be installed before importing a pack for it.',
                    ['%locale%' => $locale],
                    'Admin.International.Notification'
                ),
            ];
        }

        $errors = [];
        Language::importSfLanguagePack($locale, $archivePath, $errors);

        return $errors;
    }

    private function describe(InvalidTranslationPackException $exception): string
    {
        return match ($exception->getCode()) {
            InvalidTranslationPackException::NOT_READABLE,
            InvalidTranslationPackException::NOT_AN_ARCHIVE => $this->translator->trans(
                'The file is not a readable zip archive.',
                [],
                'Admin.International.Notification'
            ),
            InvalidTranslationPackException::MIXED_LOCALES => $this->translator->trans(
                'The archive holds more than one language. Import one language pack at a time.',
                [],
                'Admin.International.Notification'
            ),
            default => $this->translator->trans(
                'The archive is not a translation pack. It must hold a single folder named after the locale, containing only XLF files.',
                [],
                'Admin.International.Notification'
            ),
        };
    }
}
