<?php
/**
 * 2007-2018 PrestaShop.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShopBundle\Form\Admin\Configure\AdvancedParameters\Import;

use PrestaShop\PrestaShop\Core\Form\FormDataProviderInterface;
use PrestaShop\PrestaShop\Core\Import\Configuration\ImportConfigInterface;
use PrestaShop\PrestaShop\Core\Import\File\FileFinder;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * Class ImportFormDataProvider is responsible for providing Import's 1st step form data.
 */
final class ImportFormDataProvider implements FormDataProviderInterface
{
    /**
     * @var FileFinder
     */
    private $importFileFinder;

    /**
     * @var ImportConfigInterface
     */
    private $importConfig;

    /**
     * @var SessionInterface
     */
    private $session;

    /**
     * @param FileFinder $importFileFinder
     * @param ImportConfigInterface $importConfig
     * @param SessionInterface $session
     */
    public function __construct(
        FileFinder $importFileFinder,
        ImportConfigInterface $importConfig,
        SessionInterface $session
    ) {
        $this->importFileFinder = $importFileFinder;
        $this->importConfig = $importConfig;
        $this->session = $session;
    }

    /**
     * {@inheritdoc}
     */
    public function getData()
    {
        return [
            'csv' => $this->getSelectedFile(),
            'entity' => $this->importConfig->getEntityType(),
            'iso_lang' => $this->importConfig->getLanguageIso(),
            'separator' => $this->importConfig->getSeparator(),
            'multiple_value_separator' => $this->importConfig->getMultipleValueSeparator(),
            'truncate' => $this->importConfig->truncate(),
            'regenerate' => $this->importConfig->skipThumbnailRegeneration(),
            'match_ref' => $this->importConfig->matchReferences(),
            'forceIDs' => $this->importConfig->forceIds(),
            'sendemail' => $this->importConfig->sendEmail(),
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
            $this->session->set('csv', $data['csv']);
            $this->session->set('entity', $data['entity']);
            $this->session->set('iso_lang', $data['iso_lang']);
            $this->session->set('separator', $data['separator']);
            $this->session->set('multiple_value_separator', $data['multiple_value_separator']);
            $this->session->set('truncate', $data['truncate']);
            $this->session->set('match_ref', $data['match_ref']);
            $this->session->set('regenerate', $data['regenerate']);
            $this->session->set('forceIDs', $data['forceIDs']);
            $this->session->set('sendemail', $data['sendemail']);
        }

        return $errors;
    }

    /**
     * Get selected file after confirming that it is available in file system.
     *
     * @return string|null
     */
    private function getSelectedFile()
    {
        $importFiles = $this->importFileFinder->getImportFileNames();
        $selectedFile = $this->importConfig->getFileName();

        if ($selectedFile && !in_array($selectedFile, $importFiles)) {
            $selectedFile = null;
        }

        return $selectedFile;
    }
}
