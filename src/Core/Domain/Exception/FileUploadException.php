<?php

namespace PrestaShop\PrestaShop\Core\Domain\Exception;

/**
 * This exception on success case has code 0 which is equal to constant UPLOAD_ERR_OK.
 * On every other case it has one of the following error code constants:
 *
 * UPLOAD_ERR_INI_SIZE,
 * UPLOAD_ERR_FORM_SIZE,
 * UPLOAD_ERR_PARTIAL,
 * UPLOAD_ERR_NO_FILE,
 * UPLOAD_ERR_NO_TMP_DIR,
 * UPLOAD_ERR_CANT_WRITE,
 * UPLOAD_ERR_EXTENSION
 */
class FileUploadException extends DomainException
{
}
