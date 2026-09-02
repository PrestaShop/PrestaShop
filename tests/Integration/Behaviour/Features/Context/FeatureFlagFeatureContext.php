<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace Tests\Integration\Behaviour\Features\Context;

use Doctrine\ORM\EntityManager;
use Exception;
use PrestaShopBundle\Entity\FeatureFlag;
use RuntimeException;

class FeatureFlagFeatureContext extends AbstractPrestaShopFeatureContext
{
    /**
     * "When" steps perform actions, and some of them store the latest result
     * in this variable so that "Then" action can check its content
     *
     * @var mixed
     */
    protected $latestResult;

    /**
     * @Given /^I register a (disabled|enabled) feature flag "(.+)"$/
     */
    public function registerFlag(string $state, string $name): void
    {
        $doctrineEntityManager = $this->getDoctrineEntityManager();

        try {
            $newFeatureFlag = new FeatureFlag($name);

            if ($state === 'enabled') {
                $newFeatureFlag->enable();
            }

            $doctrineEntityManager->persist($newFeatureFlag);
            $doctrineEntityManager->flush();
        } catch (Exception $e) {
            $this->latestResult = $e;
        }
    }

    /**
     * @When /^I (disable|enable) feature flag "(.+)"$/
     */
    public function modifyFeatureFlagState(string $state, string $name): void
    {
        $doctrineEntityManager = $this->getDoctrineEntityManager();

        /** @var FeatureFlag $featureFlag */
        $featureFlag = $doctrineEntityManager->getRepository(FeatureFlag::class)->findOneBy(['name' => $name]);

        // We checking here because StringToBoolTransformContext transform enable/disable to boolean
        if ($state === '1' || $state === 'enable') {
            $featureFlag->enable();
        } else {
            $featureFlag->disable();
        }

        $doctrineEntityManager->flush();
    }

    /**
     * @Then /^the feature flag "(.+)" state is (disabled|enabled)$/
     */
    public function assertFeatureFlagState(string $name, string $state): void
    {
        $doctrineEntityManager = $this->getDoctrineEntityManager();

        /** @var FeatureFlag $featureFlag */
        $featureFlag = $doctrineEntityManager->getRepository(FeatureFlag::class)->findOneBy(['name' => $name]);

        if ($state === 'enabled' && !$featureFlag->isEnabled()) {
            throw new RuntimeException(sprintf('Feature flag %s is disabled although it was expected to be enabled', $name));
        } elseif ($state === 'disabled' && $featureFlag->isEnabled()) {
            throw new RuntimeException(sprintf('Feature flag %s is enabled although it was expected to be disabled', $name));
        }
    }

    /**
     * @return EntityManager
     */
    protected function getDoctrineEntityManager(): EntityManager
    {
        /** @var EntityManager $doctrineEntityManager */
        $doctrineEntityManager = CommonFeatureContext::getContainer()->get('doctrine.orm.entity_manager');

        if (!$doctrineEntityManager->isOpen()) {
            $doctrineEntityManager = CommonFeatureContext::getContainer()->get('doctrine')->resetManager();
        }

        return $doctrineEntityManager;
    }

    /**
     * @AfterStep
     */
    public function clearEntityManager()
    {
        $this->getDoctrineEntityManager()->clear();
    }

    /**
     * @Then /^I should be returned an error$/
     */
    public function assertGotErrorMessage()
    {
        if (!$this->latestResult instanceof Exception) {
            throw new Exception('Latest action did not return an error');
        }

        $this->latestResult = null;
    }
}
