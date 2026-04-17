<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Tests\Integration\Core\Translation\Storage\Provider;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Translation\MessageCatalogue;

/**
 * Test the provider of backOffice translations
 */
abstract class AbstractCatalogueLayersProviderTestCase extends KernelTestCase
{
    /**
     * @var string
     */
    protected $translationsDir;

    public function setUp(): void
    {
        self::bootKernel();
        $this->translationsDir = self::$kernel->getContainer()->getParameter('test_translations_dir');
    }

    abstract protected function getProvider(array $databaseContent = []);

    protected function getDefaultCatalogue($locale): MessageCatalogue
    {
        return $this->getProvider()->getDefaultCatalogue($locale);
    }

    protected function getFileTranslatedCatalogue($locale): MessageCatalogue
    {
        return $this->getProvider()->getFileTranslatedCatalogue($locale);
    }

    protected function getUserTranslatedCatalogue(string $locale, array $databaseContent = []): MessageCatalogue
    {
        return $this->getProvider($databaseContent)->getUserTranslatedCatalogue($locale);
    }

    /**
     * @param array $expected
     * @param MessageCatalogue $catalogue
     */
    protected function assertResultIsAsExpected(array $expected, MessageCatalogue $catalogue): void
    {
        $this->assertInstanceOf(MessageCatalogue::class, $catalogue);

        // Check integrity of translations
        $messages = $catalogue->all();
        $domains = $catalogue->getDomains();
        sort($domains);

        $this->assertSame(array_keys($expected), $domains);

        // verify that the catalogues are complete
        foreach ($expected as $expectedDomain => $expectedValues) {
            $this->assertCount($expectedValues['count'], $messages[$expectedDomain]);

            foreach ($expectedValues['translations'] as $translationKey => $translationValue) {
                $this->assertSame($translationValue, $catalogue->get($translationKey, $expectedDomain));
            }
        }
    }
}
