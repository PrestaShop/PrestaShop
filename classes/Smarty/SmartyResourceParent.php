<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

/**
 * Override module templates easily.
 */
class SmartyResourceParentCore extends Smarty_Resource_Custom
{
    /**
     * @var array<string>
     */
    public $paths;

    public function __construct(array $paths)
    {
        $this->paths = $paths;
    }

    /**
     * Fetch a template.
     *
     * @param string $name template name
     * @param string $source template source
     * @param int $mtime template modification timestamp (epoch)
     */
    protected function fetch($name, &$source, &$mtime)
    {
        foreach ($this->paths as $path) {
            if (Tools::file_exists_cache($file = $path . $name)) {
                if (_PS_MODE_DEV_) {
                    $source = implode('', [
                        '<!-- begin ' . $file . ' -->',
                        file_get_contents($file),
                        '<!-- end ' . $file . ' -->',
                    ]);
                } else {
                    $source = file_get_contents($file);
                }
                $mtime = filemtime($file);

                return;
            }
        }
    }
}
