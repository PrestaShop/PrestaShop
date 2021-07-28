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

namespace Tests\Integration\Adapter\Category\QueryHandler;

use PrestaShop\PrestaShop\Core\CommandBus\TacticianCommandBusAdapter;
use PrestaShop\PrestaShop\Core\Domain\Category\Exception\CannotEditRootCategoryException;
use PrestaShop\PrestaShop\Core\Domain\Category\Exception\CategoryNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Category\Query\GetCategoryForEditing;
use PrestaShop\PrestaShop\Core\Domain\Category\QueryResult\EditableCategory;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class GetCategoryForEditingHandlerTest extends KernelTestCase
{
    /**
     * @var object|TacticianCommandBusAdapter|null
     */
    private $commandBus;
    /**
     * @var int
     */
    private $rootCategory;

    protected function setUp(): void
    {
        self::bootKernel();

        $this->commandBus = self::$container->get('prestashop.core.command_bus');
        $this->rootCategory = (int) self::$container->get('prestashop.adapter.legacy.configuration')->get('PS_ROOT_CATEGORY');
    }

    public function testGetCategoryForEditingReturnsAnEditableCategoryIfExists(): void
    {
        $categories = self::$container->get('prestashop.adapter.form.choice_provider.category_tree_choice_provider')->getChoices();
        $existingCategoryId = $categories[0]['id_category'];
        $command = new GetCategoryForEditing((int) $existingCategoryId);

        $editableCategory = $this->commandBus->handle($command);
        $this->assertInstanceOf(EditableCategory::class, $editableCategory);
    }

    public function testGetRootCategoryForEditingThrowsAnExceptionIfCategoryDoesntExist(): void
    {
        $command = new GetCategoryForEditing(10000);
        $this->expectException(CategoryNotFoundException::class);
        $this->commandBus->handle($command);
    }

    public function testGetRootCategoryForEditingThrowsAnException(): void
    {
        $command = new GetCategoryForEditing($this->rootCategory);
        $this->expectException(CannotEditRootCategoryException::class);
        $this->commandBus->handle($command);
    }
}
