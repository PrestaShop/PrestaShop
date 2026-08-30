<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\PDF;

/**
 * Contains generated PDF content and its download filename.
 */
final class GeneratedPdf
{
    /**
     * @param string $content
     * @param string $fileName
     */
    public function __construct(
        private readonly string $content,
        private readonly string $fileName
    ) {
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function getFileName(): string
    {
        return $this->fileName;
    }
}
