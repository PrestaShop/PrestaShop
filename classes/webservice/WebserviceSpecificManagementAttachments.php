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
class WebserviceSpecificManagementAttachmentsCore implements WebserviceSpecificManagementInterface
{

    /**
     *
     * @var WebserviceOutputBuilder
     */
    protected $objOutput;

    protected $output;

    /**
     *
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
     *
     * @param WebserviceOutputBuilderCore $obj            
     * @return WebserviceSpecificManagementInterface
     */
    public function setObjectOutput(WebserviceOutputBuilderCore $obj)
    {
        $this->objOutput = $obj;
        return $this;
    }

    public function getObjectOutput()
    {
        return $this->objOutput;
    }

    public function setWsObject(WebserviceRequestCore $obj)
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

    public function manageAttachments()
    {
        
        // Pre configuration...
        if (isset($this->wsObject->urlSegment)) {
            for ($i = 1; $i < 6; $i ++) {
                if (count($this->wsObject->urlSegment) == $i) {
                    $this->wsObject->urlSegment[$i] = '';
                }
            }
        }
        
        if ($this->wsObject->urlSegment[0] != '') {
            /**
             *
             * @var ObjectModel $object
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
     * @return string[] file details
     */
    public function executeFileGetAndHead()
    {
        $a = new Attachment((int) $this->wsObject->urlSegment[2]);
        if ($a) {
            // Physical file location
            $file = _PS_DOWNLOAD_DIR_ . $a->file;
            // Check if file exists
            if (file_exists($file)) {
                // Return file details
                return array(
                    'file' => $file,
                    'mime' => $a->mime,
                    'file_name' => $a->file_name,
                    'file_size' => $a->file_size
                );
            } else {
                throw new WebserviceException(sprintf('Unable to load the attachment file for attachment %d', $this->wsObject->urlSegment[2]), array(
                    1,
                    500,
                ));
            }
        } else {
            throw new WebserviceException(sprintf('Attachment %d not found', $this->wsObject->urlSegment[2]), array(
                1,
                500,
            ));
        }
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
        $a = $this->wsObject->method == 'PUT' ? new Attachment((int) $this->wsObject->urlSegment[1]) : new Attachment();
        
        // Check form data
        if (isset($_FILES['file']) && is_uploaded_file($_FILES['file']['tmp_name'])) {
            
            // Ensure file is within allowed size limit
            if ($_FILES['file']['size'] > (Configuration::get('PS_ATTACHMENT_MAXIMUM_SIZE') * 1024 * 1024)) {
                $this->wsObject->errors[] = sprintf($this->l('The file is too large. Maximum size allowed is: %1$d kB. The file you are trying to upload is %2$d kB.'), (Configuration::get('PS_ATTACHMENT_MAXIMUM_SIZE') * 1024), number_format(($_FILES['file']['size'] / 1024), 2, '.', ''));
            } else {
                // Assign unique id
                do {
                    $uniqid = sha1(microtime());
                } while (file_exists(_PS_DOWNLOAD_DIR_ . $uniqid));
                
                $a->file_name = $_FILES['file']['name'];
                $a->file = $uniqid;
                $a->mime = $_FILES['file']['type'];
                $a->name[Configuration::get('PS_LANG_DEFAULT')] = $_POST['name'];
                
                // Move file to download dir
                if (! move_uploaded_file($_FILES['file']['tmp_name'], _PS_DOWNLOAD_DIR_ . $uniqid)) {
                    $this->wsObject->errors[] = $this->l('Failed to copy the file.');
                    unlink(_PS_DOWNLOAD_DIR_ . $a->file);
                    $a->delete();
                } else {
                    // Create/update attachment
                    if ($a->id) {
                        $a->update();
                    } else {
                        $a->add();
                    }
                    // Remember affected entity
                    $this->attachment_id = $a->id;
                }
                
                // Delete temp file
                @unlink($_FILES['file']['tmp_name']);
            }
        }
    }
}
