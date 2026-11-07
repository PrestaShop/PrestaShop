<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\PDF;

use Context;
use PrestaShop\PrestaShop\Core\PDF\Exception\PdfException;
use PrestaShop\PrestaShop\Core\PDF\PdfDocumentBuilderInterface;
use PrestaShop\PrestaShop\Core\PDF\PDFRendererEngineFactoryInterface;
use PrestaShop\PrestaShop\Core\PDF\PDFRendererInterface;

/**
 * Renders a collection of objects of a given PDF template type into a single PDF.
 *
 * Delegates HTML generation to the {@see PdfDocumentBuilderInterface} matching
 * the requested type (one document = one page group), and PDF assembly to a
 * fresh {@see PDFRendererEngineFactoryInterface}-created engine.
 */
class PDFRenderer implements PDFRendererInterface
{
    /**
     * @var array<string, mixed>
     */
    private array $data = [];

    private string $type = '';

    private string $filename = '';

    /**
     * @param iterable<PdfDocumentBuilderInterface> $documentBuilders
     */
    public function __construct(
        private readonly iterable $documentBuilders,
        private readonly PDFRendererEngineFactoryInterface $engineFactory
    ) {
    }

    public function assign(string $key, $value): PDFRendererInterface
    {
        $this->data[$key] = $value;

        return $this;
    }

    public function setType(string $type): PDFRendererInterface
    {
        $this->type = $type;

        return $this;
    }

    public function render(bool $display = true): string
    {
        $objects = $this->data['objects'] ?? [];
        $builder = $this->findDocumentBuilder();
        $bulkMode = count($objects) > 1;

        $engine = $this->engineFactory->createEngine();
        $engine->setFontForLanguage(Context::getContext()->language->iso_code);

        $this->filename = '';
        $rendered = false;

        foreach ($objects as $object) {
            $engine->startNewPageGroup();
            $document = $builder->build($object, $bulkMode);

            $engine->createHeader($document->getHeader());
            $engine->createPagination($document->getPagination());
            $engine->createContent($document->getContent());
            $engine->writePage();
            // The footer must be set after writePage(), or TCPDF will render it
            // on the next page group instead of the one it belongs to.
            $engine->createFooter($document->getFooter());
            $rendered = true;

            if ($this->filename === '') {
                $this->filename = $bulkMode ? $builder->getBulkFilename() : $document->getFilename();
            }
        }

        if (!$rendered) {
            throw new PdfException(sprintf('No PDF document could be built for type "%s": the object collection is empty.', $this->type));
        }

        if (ob_get_level() && ob_get_length() > 0) {
            ob_clean();
        }

        return $engine->outputPdf($this->filename, $display);
    }

    public function getFilename(): string
    {
        return $this->filename;
    }

    private function findDocumentBuilder(): PdfDocumentBuilderInterface
    {
        foreach ($this->documentBuilders as $documentBuilder) {
            if ($documentBuilder->supports($this->type)) {
                return $documentBuilder;
            }
        }

        throw new PdfException(sprintf('No PDF document builder supports the "%s" template type.', $this->type));
    }
}
