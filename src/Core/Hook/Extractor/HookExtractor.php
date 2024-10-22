<?php

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Hook\Extractor;

use PhpParser\Node;
use RuntimeException;
use PhpParser\NodeTraverser;
use PhpParser\ParserFactory;
use PhpParser\Error;
use PhpParser\NodeVisitorAbstract;
use Symfony\Component\Finder\Finder;
use PhpParser\PrettyPrinter\Standard;

final class HookExtractor
{
    protected $sourcePath;
    protected $excludedDirectories = [
        'var/cache',
        'vendor',
        'app',
        'bin',
        'cache',
        'config',
        'docs',
        'download',
        'img',
        'js',
        'localization',
        'mails',
        'pdf',
        'translations',
        'upload',
        'node_modules',
    ];
    protected $regexList = [
        'smarty' => [
            '/(?P<fullImplementation>\{hook\s+h\s*=\s*(["\'])(?P<hookName>.*?)\2(.*?)\})/i',
            '/(?P<fullImplementation>\{hook\s+h\s*=\s*(?P<hookName>\$[\w]+)(.*?)\})/i', // For variable hook names
        ],
        'twig' => [
            '/(?P<fullImplementation>\{\{\s*renderhook\s*\(\s*(["\'])(?P<hookName>.*?)\2(.*?)\)\s*\}\})/is',
            '/(?P<fullImplementation>\{\{\s*renderhook\s*\(\s*(?P<hookName>[\w\.\[\]\']+)\s*(.*?)\)\s*\}\})/is', // For variable hook names
            '/(?P<fullImplementation>\{\{\s*renderhooksarray\s*\(\s*(["\'])(?P<hookName>.*?)\2(.*?)\)\s*\}\})/is',
        ],
    ];

    protected $hookAliasesReferenceXml = 'https://raw.githubusercontent.com/PrestaShop/PrestaShop/develop/install-dev/data/xml/hook_alias.xml';
    protected $hookAliases = [];

    protected $hookInfosReferenceXml = 'https://raw.githubusercontent.com/PrestaShop/PrestaShop/develop/install-dev/data/xml/hook.xml';
    protected $hookInfos = [];

    public function findHooks($sourcePath)
    {
        $this->sourcePath = $sourcePath;
        $hooks = [];
        $this->extractHookAliases();
        $this->extractHookInfos();
        $this->scanDirectory($sourcePath, $hooks);

        return $hooks;
    }

    private function extractHookAliases(): void
    {
        $xmlContent = @simplexml_load_file($this->hookAliasesReferenceXml);
        if ($xmlContent === false) {
            throw new RuntimeException('Unable to load hook aliases from XML.');
        }

        foreach ($xmlContent->entities->hook_alias as $node) {
            $this->hookAliases[strtolower((string) $node->name)][] = (string) $node->name;
            $this->hookAliases[strtolower((string) $node->alias)][] = (string) $node->alias;
        }
    }

    public function getAliasesForHook($hookName): array
    {
        $hookNameLower = strtolower($hookName);

        return $this->hookAliases[$hookNameLower] ?? [];
    }

    private function extractHookInfos(): void
    {
        $xmlContent = @simplexml_load_file($this->hookInfosReferenceXml);
        if ($xmlContent === false) {
            throw new RuntimeException('Unable to load hook infos from XML.');
        }

        foreach ($xmlContent->entities->hook as $node) {
            $this->hookInfos[strtolower((string) $node->name)] = [
                'title' => (string) $node->title,
                'description' => (string) $node->descriptionn,
            ];
        }
    }

    public function getHookInfo($hookName): array
    {
        $hookNameLower = strtolower($hookName);

        return $this->hookInfos[$hookNameLower] ?? [];
    }

    public function makeRelativePath($path): string
    {
        $sourcePathWithSlash = rtrim($this->sourcePath, '/') . '/';

        return str_replace($sourcePathWithSlash, '', $path);
    }

    public function scanDirectory($sourcePath, &$hooks): void
    {
        $finder = new Finder();

        $finder->files()
            ->in($sourcePath)
            ->name('*.php')
            ->name('*.tpl')
            ->name('*.twig')
            ->exclude($this->excludedDirectories)
            ->ignoreDotFiles(true);

        foreach ($finder as $file) {
            $filePath = $file->getRealPath();
            $fileExtension = $file->getExtension();

            if ($fileExtension === 'php') {
                $this->scanPhpFileForHooks($filePath, $hooks);
            } else {
                $this->scanFileForRegexHooks($filePath, $hooks);
            }
        }
    }

    private function scanPhpFileForHooks($filePath, &$hooks)
    {
        $parserFactory = new ParserFactory();
        $parser = $parserFactory->createForNewestSupportedVersion();
        $traverser = new NodeTraverser();
        $code = file_get_contents($filePath);
        $visitor = new class($filePath, $hooks, $this, $code) extends NodeVisitorAbstract {
            private $filePath;
            private $hooks;
            private $hookFinder;
            private $code;

            public function __construct($filePath, &$hooks, $hookFinder, $code)
            {
                $this->filePath = $filePath;
                $this->hooks = &$hooks;
                $this->hookFinder = $hookFinder;
                $this->code = $code;
            }

            public function enterNode(Node $node): void
            {
                if ($node instanceof Node\Expr\StaticCall && $node->class instanceof Node\Name && $node->name instanceof Node\Identifier) {
                    if ($node->class->toString() === 'Hook' && $node->name->toString() === 'exec') {
                        /** @phpstan-ignore property.undefined */
                        $this->processHookCall($node, $node->args[0]->value, 'action');
                    }
                }

                if ($node instanceof Node\Expr\MethodCall && $node->var instanceof Node\Expr\PropertyFetch && $node->name instanceof Node\Identifier) {
                    if ($node->name->toString() === 'dispatchWithParameters') {
                        /** @phpstan-ignore property.undefined */
                        $this->processHookCall($node, $node->args[0]->value, 'action');
                    }
                }

                if ($node instanceof Node\Expr\MethodCall && $node->name instanceof Node\Identifier) {
                    if ($node->name->toString() === 'dispatchHook') {
                        /** @phpstan-ignore property.undefined */
                        $this->processHookCall($node, $node->args[0]->value, 'action');
                    }
                }
            }

            private function processHookCall(Node $node, Node $hookNameExpr, $type = 'action', $isDynamic = false)
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

                if ($hookName) {
                    $fileRelativePath = $this->hookFinder->makeRelativePath($this->filePath);

                    // Get aliases
                    $aliases = $this->hookFinder->getAliasesForHook($hookName);

                    // Get additional hook information
                    $hookInfos = $this->hookFinder->getHookInfo($hookName);

                    $hookData = [
                        'hook' => $hookName,
                        'file' => $fileRelativePath,
                        'type' => $type,
                        'dynamic' => $isDynamic,
                        'locations' => $this->hookFinder->guessLocations($hookName, [$fileRelativePath]),
                        'full_implementation' => $hookImplementation,
                        'used_parameters' => $usedParameters,
                        'title' => $hookInfos['title'] ?? '',
                        'description' => $hookInfos['description'] ?? '',
                    ];

                    if (!empty($aliases)) {
                        $hookData['aliases'] = $aliases;
                    }

                    $this->hooks[] = $hookData;
                }
            }

            public function getArgValue($expr)
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

                    return $prettyPrinter->prettyPrintExpr($expr);
                }
            }

            private function resolveDynamicHookName(Node $expr, &$isDynamic = false)
            {
                if ($expr instanceof Node\Scalar\String_) {
                    return $expr->value;
                }

                if ($expr instanceof Node\Expr\BinaryOp\Concat) {
                    $left = $this->resolveDynamicHookName($expr->left, $isDynamic);
                    $right = $this->resolveDynamicHookName($expr->right, $isDynamic);

                    return $left . $right;
                }

                if ($expr instanceof Node\Expr\FuncCall) {
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

                if ($expr instanceof Node\Expr\MethodCall) {
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

                if ($expr instanceof Node\Expr\StaticCall) {
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

            private function getNodeCode(Node $node)
            {
                $startPos = $node->getAttribute('startFilePos');
                $endPos = $node->getAttribute('endFilePos');

                if ($startPos !== null && $endPos !== null) {
                    return substr($this->code, $startPos, $endPos - $startPos + 1);
                } else {
                    // Fallback to pretty printer if positions are not available
                    $prettyPrinter = new Standard();

                    return $prettyPrinter->prettyPrintExpr($node);
                }
            }
        };

        $traverser->addVisitor($visitor);

        try {
            $code = file_get_contents($filePath);
            $ast = $parser->parse($code);
            $traverser->traverse($ast);
        } catch (Error $e) {
            echo "Error parsing $filePath: ", $e->getMessage(), "\n";
        }
    }

    private function scanFileForRegexHooks($filePath, &$hooks): void
    {
        $fileContent = file_get_contents($filePath);
        $fileExtension = pathinfo($filePath, PATHINFO_EXTENSION);

        switch ($fileExtension) {
            case 'tpl':
                $this->matchRegex($this->regexList['smarty'], $fileContent, $filePath, $hooks);
                break;
            case 'twig':
                $this->matchRegex($this->regexList['twig'], $fileContent, $filePath, $hooks);
                break;
            default:
                // If the file extension is neither 'tpl' nor 'twig', do nothing
                break;
        }
    }

    private function matchRegex($patterns, $content, $filePath, &$hooks)
    {
        foreach ($patterns as $pattern) {
            if (preg_match_all($pattern, $content, $allMatches, PREG_SET_ORDER)) {
                foreach ($allMatches as $matches) {
                    $fullImplementation = $matches['fullImplementation'] ?? '';
                    $hookName = $matches['hookName'] ?? '';
                    $isDynamic = false;

                    if (empty($hookName) || $hookName === '$hookName') {
                        continue;
                    }

                    // Trim leading and trailing quotes
                    $hookName = trim($hookName, '\'"');

                    if (preg_match('/^\$[\w]+$/', $hookName)) {
                        $isDynamic = true;
                        $varName = substr($hookName, 1);
                        if ($varName === 'hookName') {
                            $hookName = '<HookName>';
                        } else {
                            $hookName = '<Variable:' . $varName . '>';
                        }
                    }

                    if ($hookName == '<HookName>') {
                        continue;
                    }

                    // Get aliases
                    $aliases = $this->getAliasesForHook($hookName);

                    // Get additional hook information
                    $hookInfos = $this->getHookInfo($hookName);

                    $fileRelativePath = $this->makeRelativePath($filePath);
                    $hooks[] = [
                        'hook' => $hookName,
                        'file' => $fileRelativePath,
                        'type' => 'display',
                        'dynamic' => $isDynamic,
                        'locations' => $this->guessLocations($hookName, [$fileRelativePath]),
                        'full_implementation' => $fullImplementation,
                        'title' => $hookInfos['title'] ?? '',
                        'description' => $hookInfos['description'] ?? '',
                        'aliases' => $aliases,
                    ];
                }
            }
        }
    }

    public function guessLocations($hookName, $locatedIn): array
    {
        $types = [];

        if (false !== stripos($hookName, 'Admin')) {
            $types[] = 'back office';
        }

        if (false !== stripos($hookName, 'actionObject')) {
            $types[] = 'back office';
            $types[] = 'front office';

            return array_unique($types);
        }

        foreach ($locatedIn as $file) {
            if (false !== stripos($file, 'admin')) {
                $types[] = 'back office';
            }
        }

        if (empty($types)) {
            $types[] = 'front office';
        }

        return array_unique($types);
    }
}
