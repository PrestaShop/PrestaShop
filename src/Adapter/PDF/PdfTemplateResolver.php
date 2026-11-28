<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\PDF;

use Context;
use PrestaShop\PrestaShop\Core\Hook\HookDispatcherInterface;
use Twig\Environment;

/**
 * Renders a PDF Twig template block (header, footer, pagination, or one
 * document type's content/tab templates), honoring the same theme override
 * capability the legacy Smarty pdf/*.tpl files had:
 * themes/{theme}/pdf/{name}.html.twig wins over the core template if present.
 *
 * Templates are compiled from their resolved file content via
 * Environment::createTemplate() rather than through the Twig loader, so
 * overriding works per-file exactly like HTMLTemplate::getTemplate() did,
 * with no dependency on statically configured namespace paths.
 *
 * Modules can swap a template file (actionPdfTemplatePathModifier) and change
 * the variables passed to it (actionPdfTemplateVariablesModifier).
 */
final class PdfTemplateResolver
{
    private readonly string $coreTemplateDir;

    public function __construct(
        private readonly Environment $twig,
        string $projectDir,
        private readonly HookDispatcherInterface $hookDispatcher
    ) {
        $this->coreTemplateDir = rtrim($projectDir, '/') . '/src/PrestaShopBundle/Resources/views/Pdf';
    }

    /**
     * @param array<string, mixed> $variables
     */
    public function render(string $templateName, array $variables): string
    {
        return $this->renderFirstExisting([$templateName], $variables);
    }

    /**
     * Renders the first template name that actually resolves to a file, core or
     * theme-overridden (e.g. an invoice model with a country-specific variant).
     *
     * @param string[] $templateNames tried in order
     * @param array<string, mixed> $variables
     */
    public function renderFirstExisting(array $templateNames, array $variables): string
    {
        foreach ($templateNames as $templateName) {
            $path = $this->resolvePath($templateName);
            if (null !== $path) {
                $this->hookDispatcher->dispatchWithParameters('actionPdfTemplateVariablesModifier', [
                    'template_name' => $templateName,
                    'variables' => &$variables,
                ]);

                return $this->twig->createTemplate(file_get_contents($path), $templateName)->render($variables);
            }
        }

        return '';
    }

    private function resolvePath(string $templateName): ?string
    {
        $defaultPath = $this->resolveDefaultPath($templateName);
        $path = $defaultPath;
        $this->hookDispatcher->dispatchWithParameters('actionPdfTemplatePathModifier', [
            'template_name' => $templateName,
            'template_path' => &$path,
        ]);

        return is_string($path) && is_file($path) ? $path : $defaultPath;
    }

    private function resolveDefaultPath(string $templateName): ?string
    {
        $themeName = Context::getContext()->shop->theme->getName();
        $themePath = rtrim(_PS_ALL_THEMES_DIR_, '/') . '/' . $themeName . '/pdf/' . $templateName . '.html.twig';
        if (file_exists($themePath)) {
            return $themePath;
        }

        $corePath = $this->coreTemplateDir . '/' . $templateName . '.html.twig';

        return file_exists($corePath) ? $corePath : null;
    }
}
