<?php

/**
 * 2007-2016 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
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
 *  @author 	PrestaShop SA <contact@prestashop.com>
 *  @copyright  2007-2016 PrestaShop SA
 *  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */
use Symfony\Component\HttpFoundation\Response;

class WebserviceSpecificManagementAttachmentsCore implements WebserviceSpecificManagementInterface
{
    /**
     * @var WebserviceOutputBuilder
     */
    protected $objOutput;

    protected $output;

    /**
     * @var WebserviceRequest
     */
    protected $wsObject;

    /**
     * The configuration parameters of the current resource
     *
     * @var array
     */
    public $resourceConfiguration;

    protected $attachment_id;

    protected $displayFile;

    /*
     * ------------------------------------------------
     * GETTERS & SETTERS
     * ------------------------------------------------
     */

    /**
     * @param WebserviceOutputBuilder $obj
     *
     * @return WebserviceSpecificManagementInterface
     */
    public function setObjectOutput(WebserviceOutputBuilder $obj)
    {
        $this->objOutput = $obj;

        return $this;
    }

    public function getObjectOutput()
    {
        return $this->objOutput;
    }

    public function setWsObject(WebserviceRequest $obj)
    {
        $this->wsObject = $obj;

        return $this;
    }

    public function getWsObject()
    {
        return $this->wsObject;
    }

    public function getContent()
    {
        if ($this->displayFile) {
            // if displayFile is set, present the file (download)
            $this->objOutput->setHeaderParams('Content-Type', $this->displayFile['mime']);
            $this->objOutput->setHeaderParams('Content-Length', $this->displayFile['file_size']);
            $this->objOutput->setHeaderParams('Content-Disposition', 'attachment; filename="' . utf8_decode($this->displayFile['file_name']) . '"');

            return file_get_contents($this->displayFile['file']);
        } else {
            // Emulate non-specific management
            $this->wsObject->objectSpecificManagement = false;
            $this->wsObject->returnOutput();
        }
    }

    public function manage()
    {
        $this->manageAttachments();

        return $this->wsObject->getOutputEnabled();
    }

    /**
     * That part was inherited from WebserviceSpecificManagementImages (which uses deeper api path).
     * Looping for 6 segments is excessive as only the [1] and [2] indices is used for attachments.
     *
     * The explanation for the mapping can be seen further down "Available cases api/...".
     * If urlSegment[1] is set to 'file', binary operations are done (file upload/download)
     * Otherwise default webservice operations are done (read/write Model information using XML/json).
     *
     * Examples:
     * [POST] https://domain.tld/api/attachments/ only creates model information (similar to any other default api), no file information.
     * [POST] https://domain.tld/api/attachments/file creates an attachment AND uploads a file for it.
     *
     * [PUT] https://domain.tld/api/attachments/$id_attachment here urlSegment[1] is id_attachment, updates model information only.
     * [PUT] https://domain.tld/api/attachments/file/$id_attachment here urlSegment[1] is 'file' and urlSegment[2] is id_attachment, updates file (binary) only.
     *
     * [GET] https://domain.tld/api/attachments/$id_attachment gives a response in XML/json for the attachment model information.
     * [GET] https://domain.tld/api/attachments/file/$id_attachment downloads the id_attachment file
     */
    public function manageAttachments()
    {
        if (isset($this->wsObject->urlSegment)) {
            for ($i = 1; $i < 6; ++$i) {
                if (count($this->wsObject->urlSegment) == $i) {
                    $this->wsObject->urlSegment[$i] = '';
                }
            }
        }

        if ($this->wsObject->urlSegment[0] != '') {
            /**
             * @var ObjectModel
             */
            $object = new Attachment();
            $this->wsObject->resourceConfiguration = $object->getWebserviceParameters();
        }

        /*
         * Available cases api/...:
         *
         * [Utilizes default webservice handling by emulating non-specific management]
         *  attachments/ ("attachment_list")
         *      GET     (xml/json) (list of attachments)
         *      POST    (xml/json) (create/new) (not recommended, as no file will be automatically set (see attachments/file))
         *  attachments/[1,+] ("attachment_description") (N-3)
         *      GET     (xml/json)
         *      PUT     (xml/json) (update)
         *      DELETE
         *
         * [Specific management for file upload/download}
         *  attachments/file/
         *      POST    (bin) (create new attachment)
         *  attachments/file/[1,+] (file management)
         *      GET     (bin) (download file)
         *      PUT     (bin) (upload/update file)
         *      DELETE
         */
        if ($this->wsObject->urlSegment[1] == 'file') {
            // File handling (upload/download)
            switch ($this->wsObject->method) {
                case 'GET':
                case 'HEAD':
                    $this->displayFile = $this->executeFileGetAndHead();
                    break;
                case 'POST':
                case 'PUT':
                    $this->executeFileAddAndEdit();

                    // Emulate get/head to return output
                    $this->wsObject->method = 'GET';
                    $this->wsObject->urlSegment[1] = $this->attachment_id;
                    $this->wsObject->urlSegment[2] = '';
                    $this->wsObject->executeEntityGetAndHead();
                    break;
                case 'DELETE':
                    $attachment = new Attachment((int) $this->wsObject->urlSegment[1]);
                    $attachment->delete();
                    break;
            }
        } else {
            // Default handling via WebserviceRequest
            switch ($this->wsObject->method) {
                case 'GET':
                case 'HEAD':
                    $this->wsObject->executeEntityGetAndHead();
                    break;
                case 'POST':
                    $this->wsObject->executeEntityPost();
                    break;
                case 'PUT':
                    $this->wsObject->executeEntityPut();
                    break;
                case 'DELETE':
                    $this->wsObject->executeEntityDelete();
                    break;
            }
        }
        // Need to set an object for the WebserviceOutputBuilder object in any case
        // because schema need to get webserviceParameters of this object
        if (isset($object)) {
            $this->wsObject->objects['empty'] = $object;
        }
    }

    /**
     * Handles attachment file download
     *
     * @throws WebserviceException if attachment is not existing or file not available
     *
     * @return string[] file details
     */
    public function executeFileGetAndHead()
    {
        $attachment = new Attachment((int) $this->wsObject->urlSegment[2]);
        if (!$attachment) {
            throw new WebserviceException(
                sprintf(
                    'Attachment %d not found',
                    $this->wsObject->urlSegment[2]
                ),
                [
                    1,
                    Response::HTTP_INTERNAL_SERVER_ERROR,
                ]
            );
        }

        // Physical file location
        $file = _PS_DOWNLOAD_DIR_ . $attachment->file;
        // Check if file exists
        if (!file_exists($file)) {
            throw new WebserviceException(
                sprintf(
                    'Unable to load the attachment file for attachment %d',
                    $this->wsObject->urlSegment[2]
                ),
                [
                    1,
                    Response::HTTP_INTERNAL_SERVER_ERROR,
                ]
            );
        }

        // Return file details
        return array(
            'file' => $file,
            'mime' => $attachment->mime,
            'file_name' => $attachment->file_name,
            'file_size' => $attachment->file_size,
        );
    }

    /**
     * Handles file upload
     *
     * Creates new attachment or replaces existing with a new file.
     * [PUT] update existing attachment file
     * [POST] create new attachment
     */
    public function executeFileAddAndEdit()
    {
        // Load attachment with or without id depending on method
        $attachment = new Attachment($this->wsObject->method == 'PUT' ? (int) $this->wsObject->urlSegment[1] : null);

        // Check form data
        if (isset($_FILES['file']) && is_uploaded_file($_FILES['file']['tmp_name'])) {
            // Ensure file is within allowed size limit
            if ($_FILES['file']['size'] > (Configuration::get('PS_ATTACHMENT_MAXIMUM_SIZE') * 1024 * 1024)) {
                $this->wsObject->errors[] = sprintf(
                    $this->l('The file is too large. Maximum size allowed is: %1$d kB. The file you are trying to upload is %2$d kB.'),
                    Configuration::get('PS_ATTACHMENT_MAXIMUM_SIZE') * 1024,
                    number_format(($_FILES['file']['size'] / 1024), 2, '.', '')
                );
            } else {
                // Assign unique id
                if (!$attachment->id) {
                    do {
                        $uniqid = sha1(uniqid()); // must be a sha1
                    } while (file_exists(PS_DOWNLOAD_DIR . $uniqid));
                    $attachment->file = $uniqid;
                }

                $attachment->file_name = $_FILES['file']['name'];
                $attachment->mime = $_FILES['file']['type'];
                $attachment->name[Configuration::get('PS_LANG_DEFAULT')] = $_POST['name'];

                // Move file to download dir
                if (!move_uploaded_file($_FILES['file']['tmp_name'], _PS_DOWNLOAD_DIR_ . $attachment->file)) {
                    $this->wsObject->errors[] = $this->l('Failed to copy the file.');
                } else {
                    // Create/update attachment
                    if ($attachment->id) {
                        $attachment->update();
                    } else {
                        $attachment->add();
                    }
                    // Remember affected entity
                    $this->attachment_id = $attachment->id;
                }

                // Delete temp file
                @unlink($_FILES['file']['tmp_name']);
            }
        }
    }
}
