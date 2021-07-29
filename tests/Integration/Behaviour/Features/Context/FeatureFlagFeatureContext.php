<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
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
        $featureFlag = $doctrineEntityManager->getRepository('PrestaShopBundle:FeatureFlag')->findOneBy(['name' => $name]);

        if ($state === 'enable') {
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
        $featureFlag = $doctrineEntityManager->getRepository('PrestaShopBundle:FeatureFlag')->findOneBy(['name' => $name]);

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
     * @AfterScenario
     */
    public function cleanFixtures()
    {
        $doctrineEntityManager = $this->getDoctrineEntityManager();

        /** @var array<int, FeatureFlag> $allFlags */
        $allFlags = $doctrineEntityManager->getRepository('PrestaShopBundle:FeatureFlag')->findAll();
        foreach ($allFlags as $flag) {
            $doctrineEntityManager->remove($flag);
        }

        $doctrineEntityManager->flush();
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
        if (!$this->latestResult instanceof \Exception) {
            throw new Exception('Latest action did not return an error');
        }

        $this->latestResult = null;
    }
}
