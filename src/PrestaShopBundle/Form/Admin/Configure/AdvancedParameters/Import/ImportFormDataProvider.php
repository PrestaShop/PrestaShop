<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Form\Admin\Configure\AdvancedParameters\Import;

use PrestaShop\PrestaShop\Core\Import\Configuration\ImportConfigInterface;
use PrestaShop\PrestaShop\Core\Import\File\FileFinder;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Class ImportFormDataProvider is responsible for providing Import's 1st step form data.
 */
final class ImportFormDataProvider implements ImportFormDataProviderInterface
{
    /**
     * @param FileFinder $importFileFinder
     * @param RequestStack $requestStack
     */
    public function __construct(
        private readonly FileFinder $importFileFinder,
        private readonly RequestStack $requestStack
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function getData(ImportConfigInterface $importConfig)
    {
        return [
            'csv' => $this->getSelectedFile($importConfig),
            'entity' => $importConfig->getEntityType(),
            'iso_lang' => $importConfig->getLanguageIso(),
            'separator' => $importConfig->getSeparator(),
            'multiple_value_separator' => $importConfig->getMultipleValueSeparator(),
            'truncate' => $importConfig->truncate(),
            'regenerate' => $importConfig->skipThumbnailRegeneration(),
            'match_ref' => $importConfig->matchReferences(),
            'forceIDs' => $importConfig->forceIds(),
            'sendemail' => $importConfig->sendEmail(),
            'type_value' => [],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function setData(array $data)
    {
        $errors = [];
        if (empty($data['csv'])) {
            $errors[] = [
                'key' => 'To proceed, please upload a file first.',
                'domain' => 'Admin.Advparameters.Notification',
                'parameters' => [],
            ];
        } else {
            $this->requestStack->getSession()->set('csv', $data['csv']);
            $this->requestStack->getSession()->set('entity', $data['entity']);
            $this->requestStack->getSession()->set('iso_lang', $data['iso_lang']);
            $this->requestStack->getSession()->set('separator', $data['separator']);
            $this->requestStack->getSession()->set('multiple_value_separator', $data['multiple_value_separator']);
            $this->requestStack->getSession()->set('truncate', $data['truncate']);
            $this->requestStack->getSession()->set('match_ref', $data['match_ref']);
            $this->requestStack->getSession()->set('regenerate', $data['regenerate']);
            $this->requestStack->getSession()->set('forceIDs', $data['forceIDs']);
            $this->requestStack->getSession()->set('sendemail', $data['sendemail']);
        }

        return $errors;
    }

    /**
     * Get selected file after confirming that it is available in file system.
     *
     * @param ImportConfigInterface $importConfig
     *
     * @return string|null
     */
    private function getSelectedFile(ImportConfigInterface $importConfig)
    {
        $importFiles = $this->importFileFinder->getImportFileNames();
        $selectedFile = $importConfig->getFileName();
        if ($selectedFile && !in_array($selectedFile, $importFiles)) {
            $selectedFile = null;
        }

        return $selectedFile;
    }
}
