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

declare(strict_types=1);

use PrestaShop\PrestaShop\Core\File\UploadedFile;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\Response;

class WebserviceSpecificManagementAttachmentsCore implements WebserviceSpecificManagementInterface
{
    /**
     * @var WebserviceOutputBuilder
     */
    protected $objOutput;

    /**
     * @var mixed
     */
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

    /**
     * @var mixed
     */
    protected $attachment_id;

    /**
     * @var mixed
     */
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
    public function setObjectOutput(WebserviceOutputBuilderCore $obj)
    {
        $this->objOutput = $obj;

        return $this;
    }

    /**
     * Get Object Output
     */
    public function getObjectOutput()
    {
        return $this->objOutput;
    }

    /**
     * Set Webservice Object
     *
     * @param WebserviceRequestCore $obj
     */
    public function setWsObject(WebserviceRequestCore $obj)
    {
        $this->wsObject = $obj;

        return $this;
    }

    /**
     * Get Webservice Object
     */
    public function getWsObject()
    {
        return $this->wsObject;
    }

    /**
     * Get content
     *
     * @return string
     */
    public function getContent()
    {
        if ($this->displayFile) {
            // if displayFile is set, present the file (download)
            $this->getObjectOutput()->setHeaderParams('Content-Type', $this->displayFile['mime']);
            $this->getObjectOutput()->setHeaderParams('Content-Length', $this->displayFile['file_size']);
            $this->getObjectOutput()->setHeaderParams('Content-Disposition', 'attachment; filename="' . utf8_decode($this->displayFile['file_name']) . '"');

            return file_get_contents($this->displayFile['file']);
        }

        // Emulate non-specific management
        $this->getWsObject()->setObjectSpecificManagement(null);

        return '';
    }

    public function manage()
    {
        $this->manageAttachments();

        return $this->getWsObject()->getOutputEnabled();
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
        if (isset($this->getWsObject()->urlSegment)) {
            for ($i = 1; $i < 6; ++$i) {
                if (count($this->getWsObject()->urlSegment) == $i) {
                    $this->getWsObject()->urlSegment[$i] = '';
                }
            }
        }

        if ($this->getWsObject()->urlSegment[0] != '') {
            /**
             * @var ObjectModel
             */
            $object = new Attachment();
            $this->getWsObject()->resourceConfiguration = $object->getWebserviceParameters();
        }

        /*
         * Available cases api/...:
         *
         * [Utilizes default webservice handling by emulating non-specific management]
         *  attachments/ ("attachment_list")
         *      GET     (xml/json) (list of attachments)
         *  attachments/[1,+] ("attachment_description") (N-3)
         *      GET     (xml/json)
         *      PUT     (xml/json) (update)
         *      DELETE
         *
         * [Specific management for file upload/download}
         *  attachments/file/
         *      POST    (bin) (create new attachment)
         *      POST    (multipart) (create new attachment)
         *  attachments/file/[1,+] (file management)
         *      GET     (bin) (download file)
         *      PUT     (bin) (upload/update file)
         *      PUT    (multipart) (upload/update file)
         *      DELETE
         */
        if ($this->getWsObject()->urlSegment[1] == 'file') {
            // File handling (upload/download)
            switch ($this->getWsObject()->method) {
                case 'GET':
                case 'HEAD':
                    $this->displayFile = $this->executeFileGetAndHead();
                    break;
                case 'POST':
                case 'PUT':
                    $this->executeFileAddAndEdit();

                    // Emulate get/head to return output
                    $this->getWsObject()->method = 'GET';
                    $this->getWsObject()->urlSegment[1] = $this->attachment_id;
                    $this->getWsObject()->urlSegment[2] = '';
                    $this->getWsObject()->executeEntityGetAndHead();
                    break;
                case 'DELETE':
                    $attachment = new Attachment((int) $this->getWsObject()->urlSegment[1]);
                    $attachment->delete();
                    break;
            }
        } else {
            // Default handling via WebserviceRequest
            switch ($this->getWsObject()->method) {
                case 'GET':
                case 'HEAD':
                    $this->getWsObject()->executeEntityGetAndHead();
                    break;
                case 'PUT':
                    $this->getWsObject()->executeEntityPut();
                    break;
                case 'DELETE':
                    $this->getWsObject()->executeEntityDelete();
                    break;
            }
        }

        // Need to set an object for the WebserviceOutputBuilder object in any case
        // because schema need to get webserviceParameters of this object
        if (isset($object)) {
            $this->getWsObject()->objects['empty'] = $object;
        }
    }

    /**
     * Handles attachment file download
     *
     * @throws WebserviceException if attachment is not existing or file not available
     *
     * @return string[] file details
     */
    public function executeFileGetAndHead(): array
    {
        $attachment = new Attachment((int) $this->getWsObject()->urlSegment[2]);
        if (!$attachment) {
            throw new WebserviceException(
                sprintf(
                    'Attachment %d not found',
                    $this->getWsObject()->urlSegment[2]
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
                    $this->getWsObject()->urlSegment[2]
                ),
                [
                    1,
                    Response::HTTP_INTERNAL_SERVER_ERROR,
                ]
            );
        }

        // Return file details
        return [
            'file' => $file,
            'mime' => $attachment->mime,
            'file_name' => $attachment->file_name,
            'file_size' => $attachment->file_size,
        ];
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
        // Load attachment without checking the method, because of
        // PUT which is cleared the $_FILES var
        $attachmentId = null;
        if (isset($this->getWsObject()->urlSegment[2])
        ) {
            $attachmentId = (int) $this->getWsObject()->urlSegment[2];
        }

        $attachment = new Attachment($attachmentId);

        $maximumSize = ((int) Configuration::get('PS_ATTACHMENT_MAXIMUM_SIZE')) * 1024 * 1024;
        $uploadedFile = new UploadedFile(
            _PS_DOWNLOAD_DIR_,
            $maximumSize
        );

        if (isset($_FILES['file']) && is_uploaded_file($_FILES['file']['tmp_name'])) {
            // Standard HTTP upload
            $fileToUpload = $_FILES['file'];
        } else {
            // Get data from binary
            $fileToUpload = file_get_contents('php://input');
        }

        try {
            $file = $uploadedFile->upload($fileToUpload);
            if (!empty($attachment->id)) {
                unlink(PS_DOWNLOAD_DIR . $attachment->file);
            }

            $attachment->file = $file['id'];
            $attachment->file_name = $file['file_name'];
            $attachment->mime = $file['mime_type'];
            $attachment->name[Configuration::get('PS_LANG_DEFAULT')] = $_POST['name'] ?? $file['file_name'];

            if (!empty($attachment->id)) {
                $attachment->update();
            } else {
                $attachment->add();
            }
            // Remember affected entity
            $this->attachment_id = $attachment->id;
        } catch (MaximumSizeExceeded $e) {
            $this->getWsObject()->errors[] = $this->trans(
                'The file is too large. Maximum size allowed is: %1$d kB. The file you are trying to upload is %2$d kB.',
                [$maximumSize, $e->getMessage()],
                'Admin.Notifications.Error'
            );
        } catch (FailedToCopyException $e) {
            $this->getWsObject()->errors[] = $this->trans(
                'Failed to copy the file.',
                [],
                'Admin.Notifications.Error'
            );
        }
    }

    /**
     * @param string $message
     * @param array $params
     * @param string $domain
     *
     * @return string
     */
    protected function trans(string $message, array $params, string $domain)
    {
        return Context::getContext()->getTranslator()->trans($message, $params, $domain);
    }
}
