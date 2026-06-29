<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Resources\ApiPlatform\Resources;

use ApiPlatform\Metadata\ApiResource;
use PrestaShop\PrestaShop\Core\Domain\Product\Image\Command\AddProductImageCommand;
use PrestaShopBundle\ApiPlatform\Metadata\CQRSCreate;
use Symfony\Component\HttpFoundation\File\File;

#[ApiResource(
    operations: [
        new CQRSCreate(
            uriTemplate: '/test/file-upload',
            inputFormats: ['multipart' => ['multipart/form-data']],
            CQRSCommand: AddProductImageCommand::class,
            scopes: ['file_upload_write'],
            CQRSCommandMapping: [
                '[file].pathName' => '[pathName]',
            ],
        ),
        // Operation based on the same CQRS command but with JSON input, its schema must not document the file properties
        new CQRSCreate(
            uriTemplate: '/test/file-upload-json',
            CQRSCommand: AddProductImageCommand::class,
            scopes: ['file_upload_write'],
        ),
    ]
)]
class FileUploadResource
{
    public int $productId;

    public File $file;

    public ?File $optionalFile;
}
