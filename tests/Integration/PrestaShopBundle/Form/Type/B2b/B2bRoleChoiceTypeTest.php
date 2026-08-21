<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\PrestaShopBundle\Form\Type\B2b;

use Doctrine\DBAL\Configuration;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use PrestaShop\PrestaShop\Core\Context\LanguageContextBuilder;
use PrestaShopBundle\Entity\B2B\B2bRole;
use PrestaShopBundle\Entity\Repository\B2bRoleRepository;
use PrestaShopBundle\Form\Type\B2b\B2bRoleChoiceType;
use Symfony\Bridge\Doctrine\Middleware\Debug\DebugDataHolder;
use Symfony\Bridge\Doctrine\Middleware\Debug\Middleware;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Form\ChoiceList\View\ChoiceView;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
use Tests\Resources\DatabaseDump;

class B2bRoleChoiceTypeTest extends KernelTestCase
{
    private EntityManagerInterface $entityManager;
    private B2bRoleRepository $repository;
    private DebugDataHolder $debugDataHolder;

    /**
     * @var B2bRole[]
     */
    private array $addedRoles = [];

    protected function setUp(): void
    {
        parent::setUp();

        /** @var LanguageContextBuilder $languageContextBuilder */
        $languageContextBuilder = self::getContainer()->get('test_language_context_builder');
        $languageContextBuilder->setLanguageId(1);

        /* @see AlertsTrackingExtension::buildView() reads the flash bag for any root form. */
        $request = new Request();
        $request->setSession(new Session(new MockArraySessionStorage()));
        self::getContainer()->get('request_stack')->push($request);

        $this->debugDataHolder = new DebugDataHolder();
        /** @var Configuration $dbalConfiguration */
        $dbalConfiguration = self::getContainer()->get('doctrine.dbal.default_connection.configuration');
        $dbalConfiguration->setMiddlewares([
            ...$dbalConfiguration->getMiddlewares(),
            new Middleware($this->debugDataHolder, null),
        ]);

        $this->entityManager = self::getContainer()->get('doctrine')->getManagerForClass(B2bRole::class);
        $this->repository = $this->entityManager->getRepository(B2bRole::class);
    }

    protected function tearDown(): void
    {
        foreach ($this->addedRoles as $addedRole) {
            $this->entityManager->remove($addedRole);
        }

        if ([] !== $this->addedRoles) {
            $this->entityManager->flush();
            $this->addedRoles = [];
        }

        parent::tearDown();
    }

    public static function tearDownAfterClass(): void
    {
        parent::tearDownAfterClass();
        DatabaseDump::restoreTables(['b2b_role', 'b2b_role_lang']);
    }

    public static function getNewRoleIds(): iterable
    {
        yield 'single' => ['ROLE_B2B_CUSTOM_ROLE'];
        yield 'multiple' => ['ROLE_B2B_CUSTOM_ROLE_1', 'ROLE_B2B_CUSTOM_ROLE_2'];
    }

    public function testDefaultRolesAreAvailableAsChoices(): void
    {
        $roles = $this->repository->findAll();

        $this->assertNotEmpty($roles);
        $this->checkChoicesExist($roles);
    }

    #[DataProvider('getNewRoleIds')]
    public function testNewRolesAreAvailableAsChoices(string ...$roleIds): void
    {
        $newRoles = $this->addRoles(...$roleIds);

        $this->checkChoicesExist($newRoles);
    }

    public function testChoicesAreLoadedInASingleQuery(): void
    {
        $form = $this->createForm();

        $this->debugDataHolder->reset();
        $form->createView();
        $debugData = $this->debugDataHolder->getData()['default'] ?? [];

        $this->assertCount(1, $debugData);
    }

    /**
     * @param B2bRole[] $roles
     */
    private function checkChoicesExist(array $roles): void
    {
        $form = $this->createForm();
        $view = $form->createView();

        foreach ($roles as $role) {
            $this->assertTrue($this->hasChoice($view, $role), \sprintf('Role "%s" is not available as a choice', $role->getRole()));
        }
    }

    private function hasChoice(FormView $view, B2bRole $role): bool
    {
        $choices = $view->vars['choices'];

        return null !== array_find($choices, static fn (ChoiceView $choice) => $role === $choice->data);
    }

    private function createForm(): FormInterface
    {
        return self::getContainer()->get('form.factory')->create(B2bRoleChoiceType::class);
    }

    /**
     * @return B2bRole[]
     */
    private function addRoles(string ...$roles): array
    {
        $entities = [];
        foreach ($roles as $role) {
            $entities[] = $entity = (new B2bRole())->setRole($role);
            $this->entityManager->persist($entity);
        }

        $this->entityManager->flush();
        $this->addedRoles = array_merge($this->addedRoles, $entities);

        return $entities;
    }
}
