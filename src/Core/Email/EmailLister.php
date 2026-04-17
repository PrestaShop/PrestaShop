<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Email;

use PrestaShop\PrestaShop\Core\Foundation\Filesystem\FileSystem;

class EmailLister
{
    /**
     * @var FileSystem
     */
    private $filesystem;

    public function __construct(FileSystem $fs)
    {
        $this->filesystem = $fs;
    }

    /**
     * Return the list of available mails.
     *
     * @param string $dir
     *
     * @return array|null
     */
    public function getAvailableMails($dir)
    {
        if (!is_dir($dir)) {
            return null;
        }

        $mail_directory = $this->filesystem->listEntriesRecursively($dir);
        $mail_list = [];

        // Remove unwanted .html / .txt / .tpl / .php / . / ..
        foreach ($mail_directory as $mail) {
            if (str_contains($mail->getFilename(), '.')) {
                $tmp = explode('.', $mail->getFilename());

                // Check for filename existence (left part) and if extension is html (right part)
                if (!isset($tmp[0]) || (isset($tmp[1]) && $tmp[1] !== 'html')) {
                    continue;
                }

                $mail_name_no_ext = $tmp[0];
                if (!in_array($mail_name_no_ext, $mail_list)) {
                    $mail_list[] = $mail_name_no_ext;
                }
            }
        }

        return $mail_list;
    }

    /**
     * Give in input getAvailableMails(), will output a human readable and proper string name.
     *
     * @return string
     */
    public function getCleanedMailName($mail_name)
    {
        if (str_contains($mail_name, '.')) {
            $tmp = explode('.', $mail_name);

            if (!isset($tmp[0])) {
                return $mail_name;
            }

            $mail_name = $tmp[0];
        }

        return ucfirst(str_replace(['_', '-'], ' ', $mail_name));
    }
}
