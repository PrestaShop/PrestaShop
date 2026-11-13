<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Twig;

use ArrayIterator;
use IteratorAggregate;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\HttpKernel\KernelInterface;
use Traversable;

/**
 * Lists the templates warmed up by the Twig cache warmer.
 *
 * Same behaviour as Symfony's TemplateIterator, but dependency folders and files that cannot be Twig templates are
 * skipped. The whole modules directory is a Twig path (@Modules) which also contains mails, data files, assets and
 * vendor folders: in it, only *.twig files and the files of each module views folder are listed.
 */
class TemplateIterator implements IteratorAggregate
{
    public const MODULES_NAMESPACE = 'Modules';

    /**
     * Module templates live in the views folder of each module, except asset folders.
     */
    public const MODULE_VIEWS_PATH_PATTERN = '#^[^/]+/views/(?!(img|fonts|css|js)/)#i';

    public const EXCLUDED_DIRECTORIES = ['vendor', 'node_modules', '.git'];

    public const EXCLUDED_FILE_PATTERNS = [
        '*.php', '*.tpl', '*.js', '*.mjs', '*.ts', '*.map', '*.css', '*.scss', '*.less', '*.json', '*.lock',
        '*.yml', '*.yaml', '*.neon', '*.sql', '*.log', '*.md', '*.xml', '*.csv', '*.txt',
        '*.png', '*.jpg', '*.jpeg', '*.gif', '*.webp', '*.avif', '*.svg', '*.ico', '*.bmp',
        '*.woff', '*.woff2', '*.ttf', '*.eot', '*.otf',
        '*.zip', '*.gz', '*.tar', '*.tgz', '*.rar', '*.7z', '*.pdf',
        '*.mp3', '*.mp4', '*.webm', '*.mov', '*.avi',
    ];

    private ?Traversable $templates = null;

    public function __construct(
        private readonly KernelInterface $kernel,
        private readonly array $paths = [],
        private readonly ?string $defaultPath = null,
        private readonly array $namePatterns = [],
    ) {
    }

    public function getIterator(): Traversable
    {
        if (null !== $this->templates) {
            return $this->templates;
        }

        $templates = null !== $this->defaultPath ? [$this->findTemplatesInDirectory($this->defaultPath, null, ['bundles'])] : [];

        foreach ($this->kernel->getBundles() as $bundle) {
            $name = $bundle->getName();
            if (str_ends_with($name, 'Bundle')) {
                $name = substr($name, 0, -6);
            }

            $bundleTemplatesDir = is_dir($bundle->getPath() . '/Resources/views') ? $bundle->getPath() . '/Resources/views' : $bundle->getPath() . '/templates';

            $templates[] = $this->findTemplatesInDirectory($bundleTemplatesDir, $name);
            if (null !== $this->defaultPath) {
                $templates[] = $this->findTemplatesInDirectory($this->defaultPath . '/bundles/' . $bundle->getName(), $name);
            }

            $templates[] = $this->findTemplatesInDirectory($bundleTemplatesDir, '!' . $name);
        }

        foreach ($this->paths as $dir => $namespace) {
            $templates[] = $this->findTemplatesInDirectory($dir, $namespace);
        }

        return $this->templates = new ArrayIterator(array_unique(array_merge([], ...$templates)));
    }

    private function findTemplatesInDirectory(string $dir, ?string $namespace = null, array $excludeDirs = []): array
    {
        if (!is_dir($dir)) {
            return [];
        }

        $finder = Finder::create()
            ->files()
            ->followLinks()
            ->in($dir)
            ->exclude(array_merge($excludeDirs, self::EXCLUDED_DIRECTORIES))
            ->notName(self::EXCLUDED_FILE_PATTERNS)
            ->name($this->namePatterns)
        ;

        if (self::MODULES_NAMESPACE === $namespace) {
            $finder->filter(static function (SplFileInfo $file): bool {
                return 'twig' === strtolower($file->getExtension())
                    || 1 === preg_match(self::MODULE_VIEWS_PATH_PATTERN, str_replace('\\', '/', $file->getRelativePathname()));
            });
        }

        $templates = [];
        foreach ($finder as $file) {
            $templates[] = (null !== $namespace ? '@' . $namespace . '/' : '') . str_replace('\\', '/', $file->getRelativePathname());
        }

        return $templates;
    }
}
