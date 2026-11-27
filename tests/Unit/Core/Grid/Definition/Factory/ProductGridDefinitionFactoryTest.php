<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Grid\Definition\Factory;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductType;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShop\PrestaShop\Core\Employee\ContextEmployeeProviderInterface;
use PrestaShop\PrestaShop\Core\Feature\FeatureInterface;
use PrestaShop\PrestaShop\Core\Grid\Action\Row\AccessibilityChecker\AccessibilityCheckerInterface;
use PrestaShop\PrestaShop\Core\Grid\Definition\Factory\ProductGridDefinitionFactory;
use PrestaShop\PrestaShop\Core\Grid\Definition\GridDefinitionInterface;
use PrestaShop\PrestaShop\Core\Hook\HookDispatcherInterface;
use PrestaShop\PrestaShop\Core\Security\AccessCheckerInterface;
use PrestaShop\PrestaShop\Core\Shop\ShopConstraintContextInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final class ProductGridDefinitionFactoryTest extends TestCase
{
    public function testProductTypeColumnFollowsCategoryAndDisplaysTheTypeLabel(): void
    {
        $columns = $this->getDefinition()->getColumns()->toArray();
        $columnIds = array_column($columns, 'id');

        $categoryPosition = array_search('category', $columnIds, true);
        $this->assertIsInt($categoryPosition);
        $this->assertSame('product_type', $columnIds[$categoryPosition + 1]);

        $productTypeColumn = $columns[$categoryPosition + 1];
        $this->assertSame('Type', $productTypeColumn['name']);
        $this->assertSame('product_type_label', $productTypeColumn['options']['field']);
        $this->assertFalse($productTypeColumn['options']['sortable']);
    }

    public function testProductTypeFilterOffersEveryProductType(): void
    {
        $filters = $this->getDefinition()->getFilters()->all();
        $this->assertArrayHasKey('product_type', $filters);

        $filter = $filters['product_type'];
        $this->assertSame(ChoiceType::class, $filter->getType());
        $this->assertSame('product_type', $filter->getAssociatedColumn());
        $this->assertSame(
            [
                'Standard product' => ProductType::TYPE_STANDARD,
                'Product with combinations' => ProductType::TYPE_COMBINATIONS,
                'Pack of products' => ProductType::TYPE_PACK,
                'Virtual product' => ProductType::TYPE_VIRTUAL,
            ],
            $filter->getTypeOptions()['choices']
        );
        $this->assertFalse($filter->getTypeOptions()['required']);
    }

    private function getDefinition(): GridDefinitionInterface
    {
        $shopConstraintContext = $this->createMock(ShopConstraintContextInterface::class);
        $shopConstraintContext->method('getShopConstraint')->willReturn(ShopConstraint::shop(1));

        $translator = $this->createMock(TranslatorInterface::class);
        $translator->method('trans')->willReturnArgument(0);

        $contextEmployeeProvider = $this->createMock(ContextEmployeeProviderInterface::class);
        $contextEmployeeProvider->method('getProfileId')->willReturn(1);

        $factory = new ProductGridDefinitionFactory(
            $this->createMock(HookDispatcherInterface::class),
            $this->createMock(ConfigurationInterface::class),
            $this->createMock(FeatureInterface::class),
            $shopConstraintContext,
            $this->createMock(FormFactoryInterface::class),
            $this->createMock(AccessibilityCheckerInterface::class),
            $this->createMock(AccessibilityCheckerInterface::class),
            $this->createMock(AccessCheckerInterface::class),
            $contextEmployeeProvider
        );
        $factory->setTranslator($translator);

        return $factory->getDefinition();
    }
}
