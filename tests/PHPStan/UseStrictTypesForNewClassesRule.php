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

namespace Tests\PHPStan;

use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;

/**
 * @implements Rule<Node\Stmt\Class_>
 */
class UseStrictTypesForNewClassesRule implements Rule
{
    /** @var array */
    private $baseline = null;

    /**
     * {@inheritDoc}
     */
    public function getNodeType(): string
    {
        return Class_::class;
    }

    /**
     * {@inheritDoc}
     */
    public function processNode(Node $node, Scope $scope): array
    {
        $fullyQualifiedName = $scope->getNamespace() . '\\' . $node->name;
        if (in_array($fullyQualifiedName, $this->getExcludedClassList())) {
            return [];
        }

        if (!$scope->isDeclareStrictTypes()) {
            return [
                RuleErrorBuilder::message('Class should declare strict type')
                    ->build(),
            ];
        }

        return [];
    }

    /**
     * Fetch file strict-types-baseline.php which contain files
     * which do not use declare(strict_types=1)
     * but are not fixed to preserve backward compatibility.
     *
     * @return string[]
     */
    private function getExcludedClassList()
    {
        if (null === $this->baseline) {
            $baseline = require_once __DIR__ . '/strict-types-baseline.php';
            $this->baseline = $baseline;
        }

        return $this->baseline;
    }
}
