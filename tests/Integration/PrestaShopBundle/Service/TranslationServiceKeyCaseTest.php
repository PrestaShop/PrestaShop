<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace Tests\Integration\PrestaShopBundle\Service;

use Doctrine\ORM\EntityManagerInterface;
use PrestaShopBundle\Entity\Lang;
use PrestaShopBundle\Entity\Translation;
use PrestaShopBundle\Service\TranslationService;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Tests\Resources\DatabaseDump;

/**
 * The `key` column of the translation table uses a case and accent insensitive collation, so two
 * expressions that differ only by case are a single value as far as SQL equality is concerned.
 * Both wordings really do coexist in the catalogue, e.g. "Show details" and "show details" in
 * Shop.Theme.Actions.
 */
class TranslationServiceKeyCaseTest extends KernelTestCase
{
    private const DOMAIN = 'ShopThemeActions';
    private const LOWERCASE_KEY = 'show details';
    private const UPPERCASE_KEY = 'Show details';

    /** @var TranslationService */
    private $translationService;

    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var Lang */
    private $lang;

    protected function setUp(): void
    {
        parent::setUp();

        self::bootKernel();
        $container = self::getContainer();

        $this->translationService = $container->get('prestashop.service.translation');
        $this->entityManager = $container->get('doctrine')->getManager();
        $this->lang = $this->entityManager->getRepository(Lang::class)->findOneBy([]);

        $this->deleteTestedKeys();
    }

    protected function tearDown(): void
    {
        DatabaseDump::restoreTables(['translation']);

        parent::tearDown();
    }

    public function testSavingAnUppercaseKeyDoesNotOverwriteItsLowercaseSibling(): void
    {
        $this->translationService->saveTranslationMessage($this->lang, self::DOMAIN, self::LOWERCASE_KEY, 'lowercase translation');
        $this->translationService->saveTranslationMessage($this->lang, self::DOMAIN, self::UPPERCASE_KEY, 'uppercase translation');

        $this->assertSame('lowercase translation', $this->findTranslation(self::LOWERCASE_KEY));
        $this->assertSame('uppercase translation', $this->findTranslation(self::UPPERCASE_KEY));
    }

    public function testEditingOneOfTwoStoredCasesDoesNotAddADuplicateRow(): void
    {
        $this->translationService->saveTranslationMessage($this->lang, self::DOMAIN, self::LOWERCASE_KEY, 'lowercase translation');
        $this->translationService->saveTranslationMessage($this->lang, self::DOMAIN, self::UPPERCASE_KEY, 'uppercase translation');

        $this->translationService->saveTranslationMessage($this->lang, self::DOMAIN, self::UPPERCASE_KEY, 'edited uppercase translation');

        $this->assertCount(2, $this->findTestedRows());
        $this->assertSame('lowercase translation', $this->findTranslation(self::LOWERCASE_KEY));
        $this->assertSame('edited uppercase translation', $this->findTranslation(self::UPPERCASE_KEY));
    }

    /**
     * A shop that already hit this bug ends up with both cases stored, which is the state where the
     * SQL lookup matches two rows at once.
     */
    public function testSavingWhenBothCasesAreAlreadyStoredDoesNotCreateADuplicate(): void
    {
        $this->storeTranslation(self::LOWERCASE_KEY, 'lowercase translation');
        $this->storeTranslation(self::UPPERCASE_KEY, 'uppercase translation');

        $this->translationService->saveTranslationMessage($this->lang, self::DOMAIN, self::UPPERCASE_KEY, 'edited uppercase translation');

        $this->assertCount(2, $this->findTestedRows());
        $this->assertSame('lowercase translation', $this->findTranslation(self::LOWERCASE_KEY));
        $this->assertSame('edited uppercase translation', $this->findTranslation(self::UPPERCASE_KEY));
    }

    public function testResettingAnUppercaseKeyDoesNotDeleteItsLowercaseSibling(): void
    {
        $this->translationService->saveTranslationMessage($this->lang, self::DOMAIN, self::LOWERCASE_KEY, 'lowercase translation');

        $this->assertTrue($this->translationService->resetTranslationMessage($this->lang, self::DOMAIN, self::UPPERCASE_KEY));

        $this->assertSame('lowercase translation', $this->findTranslation(self::LOWERCASE_KEY));
    }

    /**
     * Shops that ran with the bug accumulated several rows under the very same key, so a reset has
     * to remove all of them - leaving one behind would report success and change nothing.
     */
    public function testResettingRemovesEveryRowStoredUnderThatKey(): void
    {
        $this->storeTranslation(self::UPPERCASE_KEY, 'first duplicate');
        $this->storeTranslation(self::UPPERCASE_KEY, 'second duplicate');

        $this->assertTrue($this->translationService->resetTranslationMessage($this->lang, self::DOMAIN, self::UPPERCASE_KEY));

        $this->assertCount(0, $this->findTestedRows());
    }

    public function testResettingAKeyStillRemovesItsOwnTranslation(): void
    {
        $this->translationService->saveTranslationMessage($this->lang, self::DOMAIN, self::LOWERCASE_KEY, 'lowercase translation');

        $this->assertTrue($this->translationService->resetTranslationMessage($this->lang, self::DOMAIN, self::LOWERCASE_KEY));

        $this->assertNull($this->findTranslation(self::LOWERCASE_KEY));
    }

    private function findTranslation(string $key): ?string
    {
        foreach ($this->findTestedRows() as $translation) {
            if ($translation->getKey() === $key) {
                return $translation->getTranslation();
            }
        }

        return null;
    }

    /**
     * Reads the rows back without relying on the collation of the `key` column, which is exactly
     * what is under test here.
     *
     * @return Translation[]
     */
    private function findTestedRows(): array
    {
        $this->entityManager->clear();

        $rows = $this->entityManager->getRepository(Translation::class)->createQueryBuilder('t')
            ->where('t.lang = :lang')->setParameter('lang', $this->lang)
            ->andWhere('t.domain = :domain')->setParameter('domain', self::DOMAIN)
            ->getQuery()->getResult();

        return array_values(array_filter(
            $rows,
            static fn (Translation $translation): bool => in_array($translation->getKey(), [self::LOWERCASE_KEY, self::UPPERCASE_KEY], true)
        ));
    }

    private function storeTranslation(string $key, string $value): void
    {
        $translation = new Translation();
        $translation->setLang($this->lang);
        $translation->setDomain(self::DOMAIN);
        $translation->setKey($key);
        $translation->setTranslation($value);

        $this->entityManager->persist($translation);
        $this->entityManager->flush();
    }

    private function deleteTestedKeys(): void
    {
        $this->entityManager->createQueryBuilder()
            ->delete(Translation::class, 't')
            ->where('t.domain = :domain')->setParameter('domain', self::DOMAIN)
            ->getQuery()->execute();
    }
}
