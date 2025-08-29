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

namespace PrestaShop\PrestaShop\Core\Module\Parser;

use PhpParser\Error;
use PhpParser\Node;
use PhpParser\Node\Expr;
use PhpParser\NodeDumper;
use PhpParser\NodeFinder;
use PhpParser\Parser;
use PhpParser\ParserFactory;

/**
 * This parser scan the PHP code of a module main class and extracts information from
 * static PHP analysis, so it is always up-to-date with the raw PHP content.
 *
 * It was done previously by using include($moduleFile) and accessing data via $module->version,
 * the problem is that ones the class has been included it remains in the same state during the
 * whole process, providing it from being updated even if the file has been changed, which can lead
 * to unexpected values during an upgrade process. This method ensure we always get the most
 * up-to-date value from the PHP files.
 */
class ModuleParser
{
    private Parser $phpParser;

    private NodeFinder $nodeFinder;

    private const DEFAULT_EXTRACTED_PROPERTIES = [
        'name',
        'tab',
        'version',
        'ps_versions_compliancy',
        'author',
        'displayName',
        'description',
        'author_uri',
        'dependencies',
        'description_full',
        'is_configurable',
        'need_instance',
        'limited_countries',
        'hooks',
    ];

    private const DEFAULT_MODULE_VALUES = [
        'author' => '',
        'displayName' => '',
        'description' => '',
        'author_uri' => '',
        'dependencies' => [],
        'description_full' => '',
        'is_configurable' => false,
        'need_instance' => true,
        'limited_countries' => [],
        'hooks' => [],
    ];

    /**
     * @param string[] $extractedModuleProperties List of properties to extract (if left empty all properties initialized in constructor are extracted)
     */
    public function __construct(
        private readonly array $extractedModuleProperties = self::DEFAULT_EXTRACTED_PROPERTIES,
    ) {
        array_map(static function ($propertyName) {
            if (!is_string($propertyName)) {
                throw new ModuleParserException('List of extracted properties is expected to be an array of string');
            }
        }, $extractedModuleProperties);
        $this->phpParser = (new ParserFactory())->create(ParserFactory::PREFER_PHP7);
        $this->nodeFinder = new NodeFinder();
    }

    /**
     * @throws ModuleParserException
     */
    public function parseModule(string $moduleClassPath): array
    {
        $statements = $this->parseModuleStatements($moduleClassPath);
        $classMethods = $this->getModuleMethods($statements);

        if (empty($classMethods['__construct'])) {
            throw new ModuleParserException('Module constructor not found');
        }

        $classAliases = $this->getClassAliases($statements);
        $classConstants = $this->getModuleConstants($statements, $classAliases);

        // First initialize default values based on which properties need to be extracted (when empty array is passed,
        // all the default properties are set)
        $modulesInfos = array_filter(
            self::DEFAULT_MODULE_VALUES,
            fn ($propertyName) => $this->shouldPropertyBeExtracted($propertyName),
            ARRAY_FILTER_USE_KEY
        );

        // Merge extracted values with default ones
        $moduleInfos = array_merge(
            $modulesInfos,
            $this->getModulePropertiesAssignments($classMethods['__construct'], $classAliases, $classConstants)
        );

        if ($this->shouldPropertyBeExtracted('hooks')) {
            $moduleInfos['hooks'] = $this->extractHooks($classMethods);
        }

        return $moduleInfos;
    }

    /**
     * Parse the whole module and dump it, very convenient for debugging.
     *
     * @throws ModuleParserException
     */
    public function dumpModuleNodes(string $moduleClassPath): string
    {
        $nodeDumper = new NodeDumper();
        $statements = $this->parseModuleStatements($moduleClassPath);

        return $nodeDumper->dump($statements);
    }

    protected function getModulePropertiesAssignments(Node\Stmt\ClassMethod $constructorMethod, array $classAliases, array $classConstants): array
    {
        $assignedProperties = [];
        /** @var Node\Stmt $stmt */
        foreach ($constructorMethod->stmts as $stmt) {
            // We only look for assignment expressions on the class properties, like "$this = something;"
            if (!$stmt instanceof Node\Stmt\Expression
                || !$stmt->expr instanceof Expr\Assign
                || !$stmt->expr->var instanceof Expr\PropertyFetch
                || !$stmt->expr->var->var instanceof Expr\Variable
                || $stmt->expr->var->var->name !== 'this'
                || !$stmt->expr->var->name instanceof Node\Identifier) {
                continue;
            }

            $propertyName = $stmt->expr->var->name->name;
            if (!$this->shouldPropertyBeExtracted($propertyName)) {
                continue;
            }

            // The returned array is only filled with non-null values
            $propertyValue = $this->getExpressionValue($stmt->expr->expr, $classAliases, $classConstants);
            if (null !== $propertyValue) {
                $assignedProperties[$propertyName] = $propertyValue;
            }
        }

        return $assignedProperties;
    }

    protected function getExpressionValue(Expr $expr, array $classAliases, array $classConstants): mixed
    {
        if ($expr instanceof Node\Scalar\String_) {
            return $expr->value;
        }
        if ($expr instanceof Node\Scalar\DNumber) {
            return $expr->value;
        }
        if ($expr instanceof Node\Scalar\LNumber) {
            return $expr->value;
        }
        if ($expr instanceof Expr\ConstFetch) {
            return $this->getConstValue($expr);
        }
        if ($expr instanceof Expr\ClassConstFetch) {
            return $this->getClassConstValue($expr, $classAliases, $classConstants);
        }
        if ($expr instanceof Expr\Array_) {
            return $this->getArrayValue($expr, $classAliases, $classConstants);
        }
        if ($expr instanceof Expr\MethodCall) {
            return $this->getMethodCallValue($expr, $classAliases, $classConstants);
        }

        return null;
    }

    protected function getMethodCallValue(Expr\MethodCall $methodCall, array $classAliases, array $classConstants): mixed
    {
        if ($methodCall->name->name === 'trans' || $methodCall->name->name === 'l') {
            if (!empty($methodCall->args)) {
                /** @var Node\Arg $firstArg */
                $firstArg = $methodCall->args[0];

                return $this->getExpressionValue($firstArg->value, $classAliases, $classConstants);
            }
        }

        return null;
    }

    protected function getClassConstValue(Expr\ClassConstFetch $constFetch, array $classAliases, array $classConstants): mixed
    {
        $className = $constFetch->class->toString();
        if (!empty($classAliases[$className])) {
            $className = $classAliases[$className];
        }

        if (class_exists($className)) {
            $constantName = $className . '::' . $constFetch->name->toString();

            return defined($constantName) ? constant($constantName) : null;
        }

        if ($className === 'self' || $className === 'static') {
            return $classConstants[$constFetch->name->toString()] ?? null;
        }

        return null;
    }

    protected function getConstValue(Expr\ConstFetch $constFetch): mixed
    {
        $constName = $constFetch->name->toString();
        // Basic const defined by the define function (like global legacy PrestaShop consts: _PS_VERSION_, _DB_PREFIX_, ...)
        if (defined($constName)) {
            return constant($constName);
        }

        return null;
    }

    protected function getArrayValue(Expr\Array_ $array, array $classAliases, array $classConstants): ?array
    {
        $arrayValue = [];
        foreach ($array->items as $index => $item) {
            $keyValue = $item->key instanceof Expr ? $this->getExpressionValue($item->key, $classAliases, $classConstants) : $index;
            if (is_scalar($keyValue)) {
                $arrayValue[$keyValue] = $this->getExpressionValue($item->value, $classAliases, $classConstants);
            }
        }

        return !empty($arrayValue) ? $arrayValue : null;
    }

    /**
     * @param Node\Stmt[] $statements
     *
     * @return array<string, Node\Stmt\ClassMethod>
     */
    protected function getModuleMethods(array $statements): array
    {
        /** @var Node\Stmt\ClassMethod[] $classMethods */
        $classMethods = $this->nodeFinder->find($statements, static function (Node $node) {
            return $node instanceof Node\Stmt\ClassMethod;
        });

        $mappedMethods = [];
        foreach ($classMethods as $classMethod) {
            $mappedMethods[$classMethod->name->name] = $classMethod;
        }

        return $mappedMethods;
    }

    /**
     * @param string $moduleClassPath
     *
     * @return Node\Stmt[]
     *
     * @throws ModuleParserException
     */
    protected function parseModuleStatements(string $moduleClassPath): array
    {
        if (!file_exists($moduleClassPath)) {
            throw new ModuleParserException('Module file not found: ' . $moduleClassPath);
        }

        $fileContent = file_get_contents($moduleClassPath);

        try {
            $statements = $this->phpParser->parse($fileContent);
            if (empty($statements)) {
                throw new ModuleParserException('Could not parse module file: ' . $moduleClassPath);
            }
        } catch (Error) {
            throw new ModuleParserException('Could not parse module file: ' . $moduleClassPath);
        }

        return $statements;
    }

    /**
     * @param Node\Stmt\ClassMethod[] $classMethods
     *
     * @return string[]
     */
    protected function extractHooks(array $classMethods): array
    {
        $hooks = [];
        foreach ($classMethods as $classMethod) {
            $methodName = $classMethod->name->name;
            if (str_starts_with(strtolower($methodName), 'hook')) {
                // Remove the prefix hook to get the hook name
                $hooks[] = lcfirst(substr($methodName, 4));
            }
        }

        return $hooks;
    }

    /**
     * @param Node\Stmt[] $statements
     *
     * @return array<string, string>
     */
    protected function getClassAliases(array $statements): array
    {
        $classAliases = [];

        // Get aliases based on use statements
        $useNodes = $this->nodeFinder->find($statements, static function (Node $node) {
            return $node instanceof Node\Stmt\UseUse;
        });
        /** @var Node\Stmt\UseUse $useStatement */
        foreach ($useNodes as $useStatement) {
            $className = $useStatement->name->toString();
            if (!class_exists($className)) {
                continue;
            }

            if (!empty($useStatement->alias)) {
                // Use alias as the key when specified (ex: use PrestaShop/Core/Version as PrestashopVersion)
                $classAliases[$useStatement->alias->toString()] = $className;
            } else {
                // Use the last part of the FQCN when no alias is specified
                $classAliases[$useStatement->name->getLast()] = $className;
            }
        }

        return $classAliases;
    }

    /**
     * @param Node\Stmt[] $statements
     * @param array<string, string> $classAliases
     *
     * @return array<string, mixed>
     */
    protected function getModuleConstants(array $statements, array $classAliases): array
    {
        // Get aliases based on module class name
        $classConstNodes = $this->nodeFinder->find($statements, static function (Node $node) {
            return $node instanceof Node\Stmt\ClassConst;
        });
        if (empty($classConstNodes)) {
            return [];
        }

        /** @var Node\Const_[] $constNodes */
        $constNodes = $this->nodeFinder->find($statements, static function (Node $node) {
            return $node instanceof Node\Const_;
        });
        if (empty($constNodes)) {
            return [];
        }

        $constants = [];
        foreach ($constNodes as $constNode) {
            $constValue = $this->getExpressionValue($constNode->value, $classAliases, []);
            if (!empty($constValue)) {
                $constants[$constNode->name->toString()] = $constValue;
            }
        }

        // Second for const that target self class
        foreach ($constNodes as $constNode) {
            $constValue = $this->getExpressionValue($constNode->value, $classAliases, $constants);
            if (!empty($constValue)) {
                $constants[$constNode->name->toString()] = $constValue;
            }
        }

        return $constants;
    }

    /**
     * We only extract properties defined for this parser in $this->extractedModuleProperties,
     * unless it is empty then all properties are extracted
     *
     * @param string $propertyName
     *
     * @return bool
     */
    protected function shouldPropertyBeExtracted(string $propertyName): bool
    {
        return empty($this->extractedModuleProperties) || in_array($propertyName, $this->extractedModuleProperties);
    }
}
