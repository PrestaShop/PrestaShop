<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Tests\Integration\PrestaShopBundle\Controller\Sell\Catalog;

use Symfony\Component\DomCrawler\Crawler;
use Tests\Integration\Core\Form\IdentifiableObject\Handler\FormHandlerChecker;
use Tests\Integration\PrestaShopBundle\Controller\FormGridControllerTestCase;
use Tests\Integration\PrestaShopBundle\Controller\TestEntityDTO;
use Tests\Resources\Resetter\FeatureResetter;

class FeatureControllerTest extends FormGridControllerTestCase
{
    private const TEST_NAME = 'testFeatureName';

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        FeatureResetter::resetFeatures();
    }

    public static function tearDownAfterClass(): void
    {
        parent::tearDownAfterClass();
        FeatureResetter::resetFeatures();
    }

    public function testIndex(): int
    {
        $features = $this->getEntitiesFromGrid();
        $this->assertNotEmpty($features);

        return $features->count();
    }

    /**
     * @depends testIndex
     *
     * @param int $initialEntityCount
     *
     * @return int
     */
    public function testCreate(int $initialEntityCount): int
    {
        $this->client->disableReboot();

        // First create feature
        $formData = [
            'feature[name][1]' => self::TEST_NAME,
        ];
        $createdFeatureId = $this->createEntityFromPage($formData);
        $this->assertNotNull($createdFeatureId);

        // Check that there is one more product in the list
        $newFeatures = $this->getEntitiesFromGrid();
        $this->assertCount($initialEntityCount + 1, $newFeatures);
        $this->assertCollectionContainsEntity($newFeatures, $createdFeatureId);

        // Check that the feature was correctly set with the expected type (the format in the form is not the same)
        $expectedFormData = [
            'feature[name][1]' => self::TEST_NAME,
        ];
        $this->assertFormValuesFromPage(
            ['featureId' => $createdFeatureId],
            $expectedFormData
        );

        return $createdFeatureId;
    }

    /**
     * @depends testCreate
     *
     * @param int $featureId
     *
     * @return int
     */
    public function testEdit(int $featureId): int
    {
        $this->client->disableReboot();

        $formData = [
            'feature[name][1]' => self::TEST_NAME,
        ];

        $this->editEntityFromPage(['featureId' => $featureId], $formData);

        $this->assertFormValuesFromPage(
            ['featureId' => $featureId],
            $formData
        );

        return $featureId;
    }

    /**
     * @depends testIndex
     * @depends testEdit
     *
     * @param int $featureId
     *
     * @return int
     */
    public function testFilters(int $initialEntityCount, int $featureId): int
    {
        // These are filters which are only supposed to match the created product, they might need to be updated
        // in case the data from fixtures change and match these test data.
        $gridFilters = [
            [
                'feature[id_feature]' => $featureId,
            ],
            [
                'feature[name]' => self::TEST_NAME,
            ],
            [
                'feature[position]' => $initialEntityCount + 1,
            ],
        ];

        foreach ($gridFilters as $testFilter) {
            $features = $this->getFilteredEntitiesFromGrid($testFilter);
            $this->assertGreaterThanOrEqual(1, count($features), sprintf(
                'Expected at least one product with filters %s',
                var_export($testFilter, true)
            ));
            $this->assertCollectionContainsEntity($features, $featureId);
        }

        return $featureId;
    }

    /**
     * {@inheritDoc}
     */
    protected function getFilterSearchButtonSelector(): string
    {
        return 'feature[actions][search]';
    }

    /**
     * {@inheritDoc}
     */
    protected function getCreateSubmitButtonSelector(): string
    {
        return 'save-button';
    }

    /**
     * {@inheritDoc}
     */
    protected function getEditSubmitButtonSelector(): string
    {
        return 'save-button';
    }

    /**
     * {@inheritDoc}
     */
    protected function generateCreateUrl(): string
    {
        return $this->router->generate('admin_features_add');
    }

    /**
     * {@inheritDoc}
     */
    protected function generateEditUrl(array $routeParams): string
    {
        return $this->router->generate('admin_features_edit', $routeParams);
    }

    /**
     * {@inheritDoc}
     */
    protected function getFormHandlerChecker(): FormHandlerChecker
    {
        /** @var FormHandlerChecker $checker */
        $checker = $this->client->getContainer()->get('prestashop.core.form.identifiable_object.handler.feature_form_handler');

        return $checker;
    }

    /**
     * {@inheritDoc}
     */
    protected function parseEntityFromRow(Crawler $tr, int $i): TestEntityDTO
    {
        return new TestEntityDTO(
            (int) trim($tr->filter('.column-id_feature')->text()),
            []
        );
    }

    /**
     * {@inheritDoc}
     */
    protected function generateGridUrl(array $routeParams = []): string
    {
        if (empty($routeParams)) {
            $routeParams = [
                'feature[offset]' => 0,
                'feature[limit]' => 10,
            ];
        }

        return $this->router->generate('admin_features_index', $routeParams);
    }

    /**
     * {@inheritDoc}
     */
    protected function getGridSelector(): string
    {
        return '#feature_grid_table';
    }
}
