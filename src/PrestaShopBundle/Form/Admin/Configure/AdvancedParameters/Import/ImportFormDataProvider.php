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
use PrestaShop\PrestaShop\Core\Import\File\FileFinder;
use PrestaShop\PrestaShop\Core\Import\ImportSettings;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * Class ImportFormDataProvider is responsible for providing Import's 1st step form data.
 */
final class ImportFormDataProvider implements FormDataProviderInterface
{
    /**
     * @var SessionInterface
     */
    private $session;

    /**
     * @var FileFinder
     */
    private $importFileFinder;

    /**
     * @var null|RequestStack current request
     */
    private $request;

    public function __construct(SessionInterface $session, FileFinder $importFileFinder, RequestStack $requestStack)
    {
        $this->session = $session;
        $this->importFileFinder = $importFileFinder;
        $this->request = $requestStack->getCurrentRequest();
    }

    /**
     * {@inheritdoc}
     */
    public function getData()
    {
        // If import entity is available in the query - grab it and preselect in the form,
        // otherwise - take it from the session.
        if (null !== $this->request && $this->request->query->has('import_type')) {
            $entity = $this->request->query->get('import_type');
        } else {
            $entity = $this->session->get('entity');
        }

        return [
            'csv' => $this->getSelectedFile(),
            'entity' => $entity,
            'iso_lang' => $this->session->get('iso_lang'),
            'separator' => $this->session->get('separator', ImportSettings::DEFAULT_SEPARATOR),
            'multiple_value_separator' => $this->session->get(
                'multiple_value_separator',
                ImportSettings::DEFAULT_MULTIVALUE_SEPARATOR
            ),
            'truncate' => $this->session->get('truncate', false),
            'regenerate' => $this->session->get('regenerate', false),
            'match_ref' => $this->session->get('match_ref', false),
            'forceIDs' => $this->session->get('forceIDs', false),
            'sendemail' => $this->session->get('sendemail', true),
        ];
    }

    /**
     * Data is persisted into session,
     * so when user comes from 2nd import step to 1st one, data is still saved.
     *
     * @param array $data
     *
     * @return array
     */
    public function setData(array $data)
    {
        $errors = [];

        if (!isset($data['csv']) || empty($data['csv'])) {
            $errors[] = [
                'key' => 'To proceed, please upload a file first.',
                'domain' => 'Admin.Advparameters.Notification',
                'parameters' => [],
            ];
        }

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

        return $errors;
    }

    /**
     * Get selected file from session if it exists
     * and check if file is available in file system.
     *
     * @return string|null
     */
    protected function getSelectedFile()
    {
        $importFiles = $this->importFileFinder->getImportFileNames();
        $selectedFile = $this->session->get('csv');

        if ($selectedFile && !in_array($selectedFile, $importFiles)) {
            $this->session->remove('csv');
            $selectedFile = null;
        }

        return $selectedFile;
    }
}
