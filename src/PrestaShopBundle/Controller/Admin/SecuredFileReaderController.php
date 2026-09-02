<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Controller\Admin;

use PrestaShopException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException;
use Symfony\Component\HttpFoundation\HeaderUtils;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/*
 * For security purpose, this controller allow you to securely display documents
 */
class SecuredFileReaderController extends AbstractController
{
    private const allowedExtensions = [
        'txt' => 'text/plain',
        'rtf' => 'application/rtf',
        'doc' => 'application/msword',
        'docx' => 'application/msword',
        'pdf' => 'application/pdf',
        'zip' => 'multipart/x-zip',
        'png' => 'image/png',
        'jpeg' => 'image/jpeg',
        'gif' => 'image/gif',
        'jpg' => 'image/jpeg',
        'webp' => 'image/webp',
    ];

    private const allowedImageExtensions = [
        'png' => 'image/png',
        'jpeg' => 'image/jpeg',
        'gif' => 'image/gif',
        'jpg' => 'image/jpeg',
        'webp' => 'image/webp',
    ];

    /** @var string */
    private $uploadDir;

    /**
     * @param string $uploadDir
     */
    public function __construct(string $uploadDir)
    {
        $this->uploadDir = $uploadDir;
    }

    /**
     * @throws PrestaShopException
     */
    public function readUploadDocument(Request $request): Response
    {
        $fileName = basename($request->query->get('fileName'));
        if (!$fileName) {
            throw new PrestaShopException('No file name specified');
        }

        $fileExtensions = explode('.', $fileName);
        if (count($fileExtensions) > 2) {
            throw new PrestaShopException('Too many extensions for ' . $fileName);
        } elseif (!array_key_exists($fileExtensions[1], self::allowedExtensions)) {
            throw new PrestaShopException('Invalid extension for ' . $fileName);
        }

        // If file is not an image, the browser directly open it as attachment
        if (!array_key_exists($fileExtensions[1], self::allowedImageExtensions)) {
            $file = file_get_contents($this->uploadDir . $fileName);
            $response = new Response($file);
            $disposition = HeaderUtils::makeDisposition(
                HeaderUtils::DISPOSITION_ATTACHMENT,
                $fileName
            );
            $response->headers->set('Content-Disposition', $disposition);
            $response->headers->set('X-Content-Type-Options', 'nosniff');
        // else we retrieve image and we display it with appropriate header
        } else {
            try {
                $response = new BinaryFileResponse($this->uploadDir . $fileName);
            } catch (FileNotFoundException) {
                throw new NotFoundHttpException();
            }

            $response->headers->set('Content-type', self::allowedExtensions[$fileExtensions[1]]);
        }

        return $response;
    }
}
