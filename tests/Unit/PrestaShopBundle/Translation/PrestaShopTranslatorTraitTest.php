<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\PrestaShopBundle\Translation;

use PHPUnit\Framework\TestCase;
use PrestaShopBundle\Translation\TranslatorComponent;
use Symfony\Component\Translation\Loader\ArrayLoader;

class PrestaShopTranslatorTraitTest extends TestCase
{
    private function buildTranslator(string $locale, array $messages, string $domain): TranslatorComponent
    {
        $translator = new TranslatorComponent($locale);
        $translator->addLoader('array', new ArrayLoader());
        $translator->addResource('array', $messages, $locale, $domain);

        return $translator;
    }

    /**
     * A translation that carries the wrong number of placeholders — e.g. the
     * real-world es-ES "Subcategorías de %s%" typo (an extra "%") — used to make
     * vsprintf() throw a ValueError, fataling every front-office page that
     * rendered the string (category, manufacturer, supplier…).
     *
     * It must now degrade gracefully to the source string formatted with the
     * given parameters, never fatal.
     */
    public function testMalformedTranslationFallsBackToSourceInsteadOfFataling(): void
    {
        $translator = $this->buildTranslator(
            'es-ES',
            ['Subcategories for %s' => 'Subcategorías de %s%'],
            'ShopThemeCatalog'
        );

        // The trait reports the broken translation with error_log() so the message never reaches
        // the HTTP response; redirect the error log to assert it was still recorded.
        $logFile = (string) tempnam(sys_get_temp_dir(), 'ps-translation-');
        $previousLog = ini_get('error_log');
        ini_set('error_log', $logFile);

        try {
            $result = $translator->trans('Subcategories for %s', ['Shoes'], 'ShopThemeCatalog', 'es-ES');
        } finally {
            ini_set('error_log', false === $previousLog ? '' : $previousLog);
        }

        $logged = (string) file_get_contents($logFile);
        unlink($logFile);

        $this->assertSame('Subcategories for Shoes', $result);
        $this->assertStringContainsString('Malformed translation', $logged, 'The malformed translation should be recorded in the error log.');
    }

    /**
     * A well-formed translation must still be formatted normally — the guard
     * only changes behaviour for the malformed case.
     */
    public function testWellFormedTranslationIsFormattedNormally(): void
    {
        $translator = $this->buildTranslator(
            'es-ES',
            ['Subcategories for %s' => 'Subcategorías de %s'],
            'ShopThemeCatalog'
        );

        $result = $translator->trans('Subcategories for %s', ['Shoes'], 'ShopThemeCatalog', 'es-ES');

        $this->assertSame('Subcategorías de Shoes', $result);
    }
}
