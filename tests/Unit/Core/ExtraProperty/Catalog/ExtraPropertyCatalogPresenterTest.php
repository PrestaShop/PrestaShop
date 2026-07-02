<?php

/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\ExtraProperty\Catalog;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\ExtraProperty\Catalog\ApiEndpointCatalogInterface;
use PrestaShop\PrestaShop\Core\ExtraProperty\Catalog\ApiEndpointEntry;
use PrestaShop\PrestaShop\Core\ExtraProperty\Catalog\ExtraPropertyCatalogPresenter;
use PrestaShop\PrestaShop\Core\ExtraProperty\Catalog\FormCatalogEntry;
use PrestaShop\PrestaShop\Core\ExtraProperty\Catalog\FormCatalogInterface;
use PrestaShop\PrestaShop\Core\ExtraProperty\Catalog\GridCatalogEntry;
use PrestaShop\PrestaShop\Core\ExtraProperty\Catalog\GridCatalogInterface;
use PrestaShop\PrestaShop\Core\ExtraProperty\Form\ExtraPropertyFormTypeMap;
use PrestaShop\PrestaShop\Core\ExtraProperty\Validation\ExtraPropertyConstraintCatalog;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class ExtraPropertyCatalogPresenterTest extends TestCase
{
    public function testAdvancedCardPayloadIsJsonReady(): void
    {
        $formCatalog = $this->createMock(FormCatalogInterface::class);
        $formCatalog->method('getAll')->willReturn([new FormCatalogEntry('product', 'Products', TextType::class)]);
        $gridCatalog = $this->createMock(GridCatalogInterface::class);
        $gridCatalog->method('getAll')->willReturn([new GridCatalogEntry('product', 'Products', [])]);
        $apiCatalog = $this->createMock(ApiEndpointCatalogInterface::class);
        $apiCatalog->method('getAll')->willReturn([new ApiEndpointEntry('/products', ['GET'], '_core')]);

        $presenter = new ExtraPropertyCatalogPresenter(
            $formCatalog,
            $gridCatalog,
            $apiCatalog,
            new ExtraPropertyConstraintCatalog(),
            new ExtraPropertyFormTypeMap(),
        );

        $payload = $presenter->presentAdvancedCard();

        $this->assertSame(['forms', 'grids', 'apis', 'defaultFormTypes'], array_keys($payload));

        $json = json_decode(json_encode($payload), true);
        // FormCatalogEntry serializes only id+label (the FQCN stays server-side).
        $this->assertSame(['id' => 'product', 'label' => 'Products'], $json['forms'][0]);
        $this->assertSame(['id' => 'product', 'label' => 'Products', 'columns' => []], $json['grids'][0]);
        $this->assertSame('/products', $json['apis'][0]['uriTemplate']);
        $this->assertArrayHasKey('bool', $json['defaultFormTypes']);
    }

    public function testValidationCardPayloadIsTheConstraintCatalog(): void
    {
        $presenter = new ExtraPropertyCatalogPresenter(
            $this->createMock(FormCatalogInterface::class),
            $this->createMock(GridCatalogInterface::class),
            $this->createMock(ApiEndpointCatalogInterface::class),
            new ExtraPropertyConstraintCatalog(),
            new ExtraPropertyFormTypeMap(),
        );

        $payload = $presenter->presentValidationCard();

        $this->assertArrayHasKey('NotBlank', $payload);
        $this->assertArrayHasKey('All', $payload);
    }
}
