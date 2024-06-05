<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
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
            } catch (FileNotFoundException $e) {
                throw new NotFoundHttpException();
            }

            $response->headers->set('Content-type', self::allowedExtensions[$fileExtensions[1]]);
        }

        return $response;
    }
}
