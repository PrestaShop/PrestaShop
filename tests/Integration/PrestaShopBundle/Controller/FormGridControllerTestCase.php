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

declare(strict_types=1);

namespace Tests\Integration\PrestaShopBundle\Controller;

use Symfony\Component\DomCrawler\Form;
use Tests\Integration\Core\Form\IdentifiableObject\Handler\FormHandlerChecker;
use Tests\Integration\PrestaShopBundle\Controller\FormFiller\FormChecker;

abstract class FormGridControllerTestCase extends GridControllerTestCase
{
    /**
     * @var FormChecker
     */
    protected $formChecker;

    /**
     * {@inheritDoc}
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->formChecker = new FormChecker();
    }

    /**
     * Create an entity by filling the form page with the provided data. You will need to implement these methods:
     *
     * - generateCreateUrl: returns the creation form url
     * - getFormHandlerChecker: return the form handler service which has been encapsulated and allows to get the created ID
     * - getCreateSubmitButtonSelector: returns the selector of the button allowing to select the form
     *
     * @param array $formData
     *
     * @return int Created entity ID
     */
    protected function createEntityFromPage(array $formData): int
    {
        $createEntityUrl = $this->generateCreateUrl();

        $this->fillAndSubmitEntityForm($createEntityUrl, $formData, $this->getCreateSubmitButtonSelector());
        $formHandlerChecker = $this->getFormHandlerChecker();

        $testEntityId = $formHandlerChecker->getLastCreatedId();
        self::assertNotNull($testEntityId);

        return $testEntityId;
    }

    /**
     * Edit an entity by filling the form page with the provided data. You will need to implement these methods:
     *
     * - generateEditUrl: returns the edit form url
     * - getEditSubmitButtonSelector: returns the selector of the button allowing to select the form
     *
     * @param array $routeParams
     * @param array $formData
     */
    protected function editEntityFromPage(array $routeParams, array $formData): void
    {
        $editEntityUrl = $this->generateEditUrl($routeParams);

        $this->fillAndSubmitEntityForm($editEntityUrl, $formData, $this->getEditSubmitButtonSelector());
    }

    /**
     * Parses the entity value from the edit page and assert it matches the expected data. You will need to implement
     * these methods:
     *
     * - generateEditUrl: returns the edit form url
     * - getEditSubmitButtonSelector: returns the selector of the button allowing to select the form
     *
     * @param array $routeParams
     * @param array $expectedFormData
     */
    protected function assertFormValuesFromPage(array $routeParams, array $expectedFormData): void
    {
        $editEntityUrl = $this->generateEditUrl($routeParams);
        $entityForm = $this->getFormFromPage($editEntityUrl, $this->getEditSubmitButtonSelector());
        $this->formChecker->checkForm($entityForm, $expectedFormData);
    }

    /**
     * @param string $formUrl
     * @param array $formData
     * @param string $formButtonSelector
     */
    protected function fillAndSubmitEntityForm(string $formUrl, array $formData, string $formButtonSelector = 'submit'): void
    {
        $filledEntityForm = $this->formFiller->fillForm(
            $this->getFormFromPage($formUrl, $formButtonSelector),
            $formData
        );

        $this->client->submit($filledEntityForm);
    }

    /**
     * @param string $formUrl
     * @param string $formButtonSelector
     *
     * @return Form
     */
    protected function getFormFromPage(string $formUrl, string $formButtonSelector): Form
    {
        $crawler = $this->client->request('GET', $formUrl);
        $this->assertResponseIsSuccessful();

        return $this->getFormByButton($crawler, $formButtonSelector);
    }

    /**
     * Returns the url of the create page.
     *
     * @return string
     */
    abstract protected function generateCreateUrl(): string;

    /**
     * Returns the selector allowing to get the create form's submit button.
     *
     * @return string
     */
    abstract protected function getCreateSubmitButtonSelector(): string;

    /**
     * In test environment all form handlers are decorated inside a FormHandlerChecker which allows us to get the
     * last created ID. This method must be implemented if you want to test your creation form, it returns the form
     * handler service used in your creation form.
     *
     * @return FormHandlerChecker
     */
    abstract protected function getFormHandlerChecker(): FormHandlerChecker;

    /**
     * Returns the url of the edit page.
     *
     * @param array $routeParams
     *
     * @return string
     */
    abstract protected function generateEditUrl(array $routeParams): string;

    /**
     * Returns the selector allowing to get the edit form's submit button.
     *
     * @return string
     */
    abstract protected function getEditSubmitButtonSelector(): string;
}
