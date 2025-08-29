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

namespace PrestaShop\PrestaShop\Core\Hook\Extractor;

use PhpParser\Error;
use PhpParser\NodeTraverser;
use PhpParser\ParserFactory;
use RuntimeException;
use Symfony\Component\Finder\Finder;

final class HookExtractor
{
    protected array $hooks = [];
    protected array $excludedDirectories = [
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
        'tests',
    ];
    protected array $regexList = [
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

    protected string $hookAliasesReferenceXml = 'https://raw.githubusercontent.com/PrestaShop/PrestaShop/develop/install-dev/data/xml/hook_alias.xml';
    protected array $hookAliases = [];

    protected string $hookInfosReferenceXml = 'https://raw.githubusercontent.com/PrestaShop/PrestaShop/develop/install-dev/data/xml/hook.xml';
    protected array $hookInfos = [];

    public function findHooks(): array
    {
        $this->extractHookAliases();
        $this->extractHookInfos();
        $this->scanDirectory();

        return $this->hooks;
    }

    protected function extractHookAliases(): void
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

    protected function getAliasesForHook($hookName): array
    {
        $hookNameLower = strtolower($hookName);

        return $this->hookAliases[$hookNameLower] ?? [];
    }

    protected function extractHookInfos(): void
    {
        $xmlContent = @simplexml_load_file($this->hookInfosReferenceXml);
        if ($xmlContent === false) {
            throw new RuntimeException('Unable to load hook infos from XML.');
        }

        foreach ($xmlContent->entities->hook as $node) {
            $this->hookInfos[strtolower((string) $node->name)] = [
                'title' => (string) $node->title,
                'description' => (string) $node->description,
            ];
        }
    }

    protected function getHookInfo($hookName): array
    {
        $hookNameLower = strtolower($hookName);

        return $this->hookInfos[$hookNameLower] ?? [];
    }

    protected function makeRelativePath($path): string
    {
        $sourcePathWithSlash = rtrim(_PS_ROOT_DIR_, '/') . '/';

        return str_replace($sourcePathWithSlash, '', $path);
    }

    protected function scanDirectory(): void
    {
        $finder = new Finder();

        $finder->files()
            ->in(_PS_ROOT_DIR_)
            ->name('*.php')
            ->name('*.tpl')
            ->name('*.twig')
            ->exclude($this->excludedDirectories)
            ->ignoreDotFiles(true);

        foreach ($finder as $file) {
            $filePath = $file->getRealPath();
            $fileExtension = $file->getExtension();

            if ($fileExtension === 'php') {
                $this->scanPhpFileForHooks($filePath);
            } else {
                $this->scanFileForRegexHooks($filePath);
            }
        }
    }

    private function scanPhpFileForHooks($filePath): void
    {
        $parserFactory = new ParserFactory();
        $parser = $parserFactory->createForNewestSupportedVersion();
        $traverser = new NodeTraverser();
        $code = file_get_contents($filePath);
        $visitor = new HookParserVisitor($filePath, $code);

        $traverser->addVisitor($visitor);

        try {
            $code = file_get_contents($filePath);
            $ast = $parser->parse($code);
            $traverser->traverse($ast);
            $visitedHooks = $this->enrichVisitedHooks($visitor->getHooks());

            $this->hooks = array_merge($this->hooks, $visitedHooks);
        } catch (Error $e) {
            echo "Error parsing $filePath: ", $e->getMessage(), "\n";
        }
    }

    private function enrichVisitedHooks(array $visitedHooks): array
    {
        $enrichedHooks = [];
        foreach ($visitedHooks as $hookName => $hookInfos) {
            $infosToAdd = [];
            $fileRelativePath = $this->makeRelativePath($hookInfos['filepath']);
            $infos = $this->getHookInfo($hookName);
            $aliases = $this->getAliasesForHook($hookName);

            $infosToAdd['file'] = $fileRelativePath;
            $infosToAdd['locations'] = $this->guessLocations($hookName, [$fileRelativePath]);
            $infosToAdd['title'] = $infos['title'] ?? '';
            $infosToAdd['description'] = $infos['description'] ?? '';
            if (!empty($aliases)) {
                $infosToAdd['aliases'] = $aliases;
            }

            $enrichedHooks[$hookName] = array_merge($hookInfos, $infosToAdd);
        }

        return $enrichedHooks;
    }

    private function scanFileForRegexHooks($filePath): void
    {
        $fileContent = file_get_contents($filePath);
        $fileExtension = pathinfo($filePath, PATHINFO_EXTENSION);

        switch ($fileExtension) {
            case 'tpl':
                $this->matchRegex($this->regexList['smarty'], $fileContent, $filePath);
                break;
            case 'twig':
                $this->matchRegex($this->regexList['twig'], $fileContent, $filePath);
                break;
            default:
                // If the file extension is neither 'tpl' nor 'twig', do nothing
                break;
        }
    }

    private function matchRegex($patterns, $content, $filePath): void
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
                    $this->hooks[] = [
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
