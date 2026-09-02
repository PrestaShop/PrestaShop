<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Integration\PrestaShopBundle\Command;

use Doctrine\ORM\EntityManagerInterface;
use PrestaShop\PrestaShop\Core\FeatureFlag\FeatureFlagStateCheckerInterface;
use PrestaShopBundle\Entity\FeatureFlag;
use PrestaShopBundle\Entity\Repository\FeatureFlagRepository;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\HttpKernel\KernelInterface;
use Tests\Resources\DatabaseDump;

class FeatureFlagCommandTest extends KernelTestCase
{
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        $kernel = self::bootKernel();
        DatabaseDump::restoreTables(['feature_flag']);
        $featureFlag = new FeatureFlag('test_feature_flag');
        /** @var EntityManagerInterface $entityManager */
        $entityManager = $kernel->getContainer()->get('doctrine.orm.entity_manager');
        $entityManager->persist($featureFlag);
        $entityManager->flush();
    }

    public static function tearDownAfterClass(): void
    {
        parent::tearDownAfterClass();
        DatabaseDump::restoreTables(['feature_flag']);
    }

    public function testList(): void
    {
        $kernel = self::bootKernel();
        /** @var EntityManagerInterface $entityManager */
        $entityManager = $kernel->getContainer()->get('doctrine.orm.entity_manager');
        /** @var FeatureFlagRepository $featureFlagRepository */
        $featureFlagRepository = $entityManager->getRepository(FeatureFlag::class);
        $featureFlagStateChecker = $kernel->getContainer()->get(FeatureFlagStateCheckerInterface::class);
        $featureFlags = $featureFlagRepository->findAll();
        /** @var FeatureFlag $featureFlag */
        foreach ($featureFlags as $featureFlag) {
            // This method is based on the output of the list action so it indirectly tests it
            $flagState = $this->getFeatureFlagState($kernel, $featureFlag->getName());
            $this->assertEquals($flagState, $featureFlagStateChecker->isEnabled($featureFlag->getName()));
        }
    }

    public function testEnable(): void
    {
        $kernel = self::bootKernel();
        $this->assertFalse($this->getFeatureFlagState($kernel, 'test_feature_flag'));

        $application = new Application($kernel);
        $command = $application->find('prestashop:feature-flag');
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'action' => 'enable',
            'feature_flag' => 'test_feature_flag',
        ]);
        $this->assertTrue($this->getFeatureFlagState($kernel, 'test_feature_flag'));
    }

    public function testDisable(): void
    {
        $kernel = self::bootKernel();
        $this->assertTrue($this->getFeatureFlagState($kernel, 'test_feature_flag'));

        $application = new Application($kernel);
        $command = $application->find('prestashop:feature-flag');
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'action' => 'disable',
            'feature_flag' => 'test_feature_flag',
        ]);
        $this->assertFalse($this->getFeatureFlagState($kernel, 'test_feature_flag'));
    }

    private function getFeatureFlagState(KernelInterface $kernel, string $featureFlag): bool
    {
        // The feature flag manager has internal cache, however it is also a resettable service, so if we reset the kernel so will the manager
        $kernel->shutdown();
        $kernel->boot();
        $application = new Application($kernel);
        $command = $application->find('prestashop:feature-flag');
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'action' => 'list',
        ]);

        // the output of the command in the console
        $output = $commandTester->getDisplay();
        $regexp = sprintf('/\|[ ]+%s[ ]+\|[ ]+([^ ]+)[ ]+\|[ ]+([^ ]+)[ ]+\|/', $featureFlag);
        $matches = [];
        preg_match($regexp, $output, $matches);
        $type = $matches[1];
        // DB layer selected
        $this->assertTrue(strpos($type, '[db]') !== false);
        $state = $matches[2];
        $this->assertTrue(in_array($state, ['Enabled', 'Disabled']));

        return $state === 'Enabled';
    }
}
