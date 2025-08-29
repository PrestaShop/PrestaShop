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

namespace PrestaShop\PrestaShop\Core\Hook\Extractor;

use InvalidArgumentException;
use PhpParser\Node;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\NodeVisitorAbstract;
use PhpParser\PrettyPrinter\Standard;

class HookParserVisitor extends NodeVisitorAbstract
{
    private string $filePath;
    private array $hooks;
    private string $code;

    public function __construct(string $filePath, string $code)
    {
        $this->filePath = $filePath;
        $this->hooks = [];
        $this->code = $code;
    }

    public function enterNode(Node $node): int|Node|null
    {
        if ($node instanceof StaticCall && $node->class instanceof Node\Name && $node->name instanceof Node\Identifier) {
            if ($node->class->toString() === 'Hook' && $node->name->toString() === 'exec') {
                /* @phpstan-ignore property.undefined */
                $this->processHookCall($node, $node->args[0]->value, 'action');
            }
        }

        if ($node instanceof MethodCall && $node->var instanceof Node\Expr\PropertyFetch && $node->name instanceof Node\Identifier) {
            if ($node->name->toString() === 'dispatchWithParameters') {
                /* @phpstan-ignore property.undefined */
                $this->processHookCall($node, $node->args[0]->value, 'action');
            }
        }

        if ($node instanceof MethodCall && $node->name instanceof Node\Identifier) {
            if ($node->name->toString() === 'dispatchHook') {
                /* @phpstan-ignore property.undefined */
                $this->processHookCall($node, $node->args[0]->value, 'action');
            }
        }

        return null;
    }

    private function processHookCall(Node $node, Node $hookNameExpr, string $type = 'action', bool $isDynamic = false): void
    {
        $hookName = $this->resolveDynamicHookName($hookNameExpr, $isDynamic);
        $hookImplementation = $this->getNodeCode($node);

        $parameterNames = [
            'hook_name',
            'hook_args',
            'id_module',
            'array_return',
            'check_exceptions',
            'use_push',
            'id_shop',
            'chain',
        ];

        $usedParameters = [];
        if ($node instanceof FuncCall || $node instanceof MethodCall || $node instanceof StaticCall) {
            foreach ($node->args as $index => $arg) {
                if ($index >= count($parameterNames)) {
                    break;
                }
                $paramName = $parameterNames[$index];
                if ($paramName === 'hook_name') {
                    continue;
                }
                $argValue = $this->getArgValue($arg->value);
                $usedParameters[$paramName] = $argValue;
            }
        } else {
            throw new InvalidArgumentException('Node does not have args property.');
        }

        if ($hookName) {
            $hookData = [
                'hook' => $hookName,
                'filepath' => $this->filePath,
                'type' => $type,
                'dynamic' => $isDynamic,
                'full_implementation' => $hookImplementation,
                'used_parameters' => $usedParameters,
            ];

            $this->hooks[$hookData['hook']] = $hookData;
        }
    }

    public function getArgValue(Node $expr): string
    {
        if ($expr instanceof Node\Scalar\String_) {
            return "'" . $expr->value . "'";
        } elseif ($expr instanceof Node\Expr\ConstFetch) {
            return $expr->name->toString();
        } elseif ($expr instanceof Node\Scalar\LNumber || $expr instanceof Node\Scalar\DNumber) {
            return (string) $expr->value;
        } elseif ($expr instanceof Node\Expr\Array_) {
            $prettyPrinter = new Standard();

            return $prettyPrinter->prettyPrintExpr($expr);
        } elseif ($expr instanceof Node\Expr\Variable) {
            return '$' . $expr->name;
        } else {
            $prettyPrinter = new Standard();
            if ($expr instanceof Node\Stmt\Expression) {
                return $prettyPrinter->prettyPrint([$expr->expr]);
            } elseif ($expr instanceof Node\Expr) {
                return $prettyPrinter->prettyPrint([$expr]);
            } else {
                throw new InvalidArgumentException('Unsupported node type.');
            }
        }
    }

    /**
     * @return array
     */
    public function getHooks(): array
    {
        return $this->hooks;
    }

    private function resolveDynamicHookName(Node $expr, &$isDynamic = false): string
    {
        if ($expr instanceof Node\Scalar\String_) {
            return $expr->value;
        }

        if ($expr instanceof Node\Expr\BinaryOp\Concat) {
            $left = $this->resolveDynamicHookName($expr->left, $isDynamic);
            $right = $this->resolveDynamicHookName($expr->right, $isDynamic);

            return $left . $right;
        }

        if ($expr instanceof FuncCall) {
            $funcName = $expr->name->toString();

            if (isset($expr->args[0])) {
                $argValue = $this->resolveDynamicHookName($expr->args[0]->value, $isDynamic);

                // Handle specific functions
                if ($funcName === 'ucfirst') {
                    // Apply ucfirst to the resolved argument
                    // Since placeholders like <Action> are case-insensitive, we can return the same placeholder
                    return $argValue;
                }

                if ($funcName === 'get_class') {
                    // Handle get_class($this)
                    return '<ClassName>';
                }

                // Handle other functions if necessary
                $isDynamic = true;

                return '<FunctionCall:' . $funcName . '>';
            }

            // If no arguments, return a placeholder
            $isDynamic = true;

            return '<FunctionCall:' . $funcName . '>';
        }

        if ($expr instanceof MethodCall) {
            $methodName = $expr->name->toString();

            // Handle specific dynamic cases
            if ($methodName === 'getFullyQualifiedName') {
                $isDynamic = true;

                return '<ClassName>';
            }
            if ($methodName === 'getId') {
                $isDynamic = true;

                return '<DefinitionId>';
            }
            if ($methodName === 'legacyControllerName' || $methodName === 'getLegacyControllerName') {
                $isDynamic = true;

                return '<LegacyControllerName>';
            }
            if ($methodName === 'getName' || $methodName === 'getFormName') {
                $isDynamic = true;

                return '<FormName>';
            }

            // Handle camelize method specifically
            if ($methodName === 'camelize') {
                // Process the first argument of camelize
                if (isset($expr->args[0])) {
                    $argumentValue = $this->resolveDynamicHookName($expr->args[0]->value, $isDynamic);

                    // Check if the argument is one of the known placeholders
                    $knownPlaceholders = ['<FormName>', '<DefinitionId>', '<LegacyControllerName>', '<HookName>', '<Action>', '<ClassName>'];
                    if (in_array($argumentValue, $knownPlaceholders, true)) {
                        $isDynamic = true;

                        return $argumentValue;
                    }

                    // Add more conditions if needed
                    $isDynamic = true;

                    return '<CamelizedValue>';
                }
            }

            // If method call is not recognized, return a placeholder
            $isDynamic = true;

            return '<MethodCall>';
        }

        if ($expr instanceof StaticCall) {
            $methodName = $expr->name->toString();

            // Handle static method calls
            if ($methodName === 'camelize') {
                // Process the first argument of camelize
                if (isset($expr->args[0])) {
                    $argumentValue = $this->resolveDynamicHookName($expr->args[0]->value, $isDynamic);

                    // Check for known placeholders
                    $knownPlaceholders = ['<FormName>', '<DefinitionId>', '<LegacyControllerName>', '<HookName>', '<Action>', '<ClassName>'];
                    if (in_array($argumentValue, $knownPlaceholders, true)) {
                        $isDynamic = true;

                        return $argumentValue;
                    }

                    $isDynamic = true;

                    return '<CamelizedValue>';
                }
            }

            // Handle other static methods if necessary
            $isDynamic = true;

            return '<StaticMethodCall>';
        }

        if ($expr instanceof Node\Expr\PropertyFetch) {
            $propertyName = $expr->name->toString();

            if ($propertyName === 'gridId') {
                $isDynamic = true;

                return '<DefinitionId>';
            }
            if ($propertyName === 'controller_name') {
                $isDynamic = true;

                return '<Controller>';
            }
            if ($propertyName === 'action') {
                $isDynamic = true;

                return '<Action>';
            }

            // Handle other properties if necessary
            $isDynamic = true;

            return '<Property:' . $propertyName . '>';
        }

        if ($expr instanceof Node\Expr\Variable) {
            $varName = $expr->name;
            $isDynamic = true;

            // Check for specific variable names
            if ($varName === 'hookName') {
                return '<HookName>';
            }
            if ($varName === 'gridId') {
                return '<DefinitionId>';
            }
            if ($varName === 'action') {
                return '<Action>';
            }

            // Handle other specific variables if needed

            // Default placeholder for variables
            return '<Variable:' . $varName . '>';
        }

        // Return a generic placeholder if unable to resolve
        $isDynamic = true;

        return '<Unknown>';
    }

    private function getNodeCode(Node $node): string
    {
        $startPos = $node->getAttribute('startFilePos');
        $endPos = $node->getAttribute('endFilePos');

        if ($startPos !== null && $endPos !== null) {
            return substr($this->code, $startPos, $endPos - $startPos + 1);
        } else {
            // Fallback to pretty printer if positions are not available
            $prettyPrinter = new Standard();

            if ($node instanceof Node\Stmt\Expression) {
                return $prettyPrinter->prettyPrint([$node->expr]);
            } elseif ($node instanceof Node\Expr) {
                return $prettyPrinter->prettyPrint([$node]);
            } else {
                throw new InvalidArgumentException('Unsupported node type.');
            }
        }
    }
}
