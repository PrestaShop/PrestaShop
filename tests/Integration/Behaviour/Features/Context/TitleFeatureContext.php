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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace Tests\Integration\Behaviour\Features\Context;

use Behat\Gherkin\Node\TableNode;
use Gender;
use PrestaShop\PrestaShop\Core\Form\FormChoiceProviderInterface;
use RuntimeException;
use Tests\Integration\Behaviour\Features\Context\Util\NoExceptionAlthoughExpectedException;
use Tests\Integration\Behaviour\Features\Context\Util\PrimitiveUtils;
use Validate;

class TitleFeatureContext extends AbstractPrestaShopFeatureContext
{
    /**
     * @var int default lang id from configs
     */
    private $defaultLangId;

    public function __construct()
    {
        $configuration = CommonFeatureContext::getContainer()->get('prestashop.adapter.legacy.configuration');
        $this->defaultLangId = (int) $configuration->get('PS_LANG_DEFAULT');
    }

    /**
     * @When I define an uncreated title :titleReference
     *
     * @param string $titleReference
     */
    public function defineUnCreatedTitle(string $titleReference): void
    {
        SharedStorage::getStorage()->set($titleReference, 1234567);
    }

    /**
     * @When I add a new title :titleReference with the following properties:
     *
     * @param string $titleReference
     * @param TableNode $table
     */
    public function createTitle(string $titleReference, TableNode $table): void
    {
        $data = $table->getRowsHash();

        /** @var FormChoiceProviderInterface $provider */
        $provider = CommonFeatureContext::getContainer()->get('prestashop.core.form.choice_provider.gender_choice_provider');
        $availableGendersTypes = $provider->getChoices();
        if (!isset($availableGendersTypes[$data['type']])) {
            throw new RuntimeException(sprintf('Gender type "%s" is not available.', $data['type']));
        }

        $title = new Gender();
        $title->name[$this->defaultLangId] = $data['name'];
        $title->type = $availableGendersTypes[$data['type']];
        $title->add();

        SharedStorage::getStorage()->set($titleReference, $title->id);
    }

    /**
     * @When I edit the title :titleReference with the following properties:
     *
     * @param string $titleReference
     * @param TableNode $table
     */
    public function editState(string $titleReference, TableNode $table): void
    {
        $titleId = SharedStorage::getStorage()->get($titleReference);
        $title = new Gender($titleId);

        /** @var FormChoiceProviderInterface $provider */
        $provider = CommonFeatureContext::getContainer()->get('prestashop.core.form.choice_provider.gender_choice_provider');
        $availableGendersTypes = $provider->getChoices();

        $data = $table->getRowsHash();
        if (isset($data['name'])) {
            $title->name[$this->defaultLangId]->name = $data['name'];
        }
        if (isset($data['type'])) {
            if (!isset($availableGendersTypes[$data['type']])) {
                throw new RuntimeException(sprintf('Gender type "%s" is not available.', $data['type']));
            }
            $title->type = $availableGendersTypes[$data['type']];
        }
        $title->save();

        SharedStorage::getStorage()->set($titleReference, $title->id);
    }

    /**
     * @Then /^the title "(.+)" should be deleted$/
     *
     * @param string $titleReference
     */
    public function assertTitleIsDeleted(string $titleReference): void
    {
        /** @var int $titleId */
        $titleId = SharedStorage::getStorage()->get($titleReference);
        $title = new Gender((int) $titleId);

        $isFound = Validate::isLoadedObject($title);
        if ($isFound) {
            throw new NoExceptionAlthoughExpectedException(sprintf('Title %s exists, but it was expected to be deleted', $titleReference));
        } else {
            SharedStorage::getStorage()->clear($titleReference);
        }
    }

    /**
     * @Then /^the title "(.+)" should not be deleted$/
     *
     * @param string $titleReference
     */
    public function assertTitleIsNotDeleted(string $titleReference): void
    {
        /** @var int $titleId */
        $titleId = SharedStorage::getStorage()->get($titleReference);
        $title = new Gender((int) $titleId);

        $isFound = Validate::isLoadedObject($title);
        if (!$isFound) {
            throw new NoExceptionAlthoughExpectedException(sprintf('Title %s doesn\'t exist, but it was expected to be existing', $titleReference));
        }
    }

    /**
     * @Then titles :titleReferences should be deleted
     *
     * @param string $titleReferences
     */
    public function assertTitlesAreDeleted(string $titleReferences): void
    {
        foreach (PrimitiveUtils::castStringArrayIntoArray($titleReferences) as $titleReference) {
            $this->assertTitleIsDeleted($titleReference);
        }
    }
}
