<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Form\Admin\Configure\AdvancedParameters\Administration;

use PrestaShop\PrestaShop\Core\Configuration\DataConfigurationInterface;
use PrestaShop\PrestaShop\Core\Configuration\UploadSizeConfigurationInterface;
use PrestaShop\PrestaShop\Core\Form\FormDataProviderInterface;
use PrestaShopBundle\Form\Exception\DataProviderException;
use PrestaShopBundle\Form\Exception\InvalidConfigurationDataError;
use PrestaShopBundle\Form\Exception\InvalidConfigurationDataErrorCollection;

/**
 * This class is responsible of managing the data manipulated using Upload Quota form
 * in "Configure > Advanced Parameters > Administration" page.
 */
final class UploadQuotaDataProvider implements FormDataProviderInterface
{
    /**
     * @var DataConfigurationInterface
     */
    private $dataConfiguration;
    private UploadSizeConfigurationInterface $uploadSizeConfiguration;

    public function __construct(
        DataConfigurationInterface $dataConfiguration,
        UploadSizeConfigurationInterface $uploadSizeConfiguration
    ) {
        $this->dataConfiguration = $dataConfiguration;
        $this->uploadSizeConfiguration = $uploadSizeConfiguration;
    }

    /**
     * {@inheritdoc}
     */
    public function getData()
    {
        return $this->dataConfiguration->getConfiguration();
    }

    /**
     * {@inheritdoc}
     */
    public function setData(array $data)
    {
        $this->validate($data);

        return $this->dataConfiguration->updateConfiguration($data);
    }

    /**
     * Perform validations on form data.
     *
     * @param array $data
     */
    private function validate(array $data): void
    {
        $errors = new InvalidConfigurationDataErrorCollection();

        if (isset($data[UploadQuotaType::FIELD_MAX_SIZE_ATTACHED_FILES])) {
            $maxSizeAttachedFile = $data[UploadQuotaType::FIELD_MAX_SIZE_ATTACHED_FILES];
            if (!is_numeric($maxSizeAttachedFile) || $maxSizeAttachedFile < 0 || $this->convertBytes($maxSizeAttachedFile . 'm') > $this->uploadSizeConfiguration->getMaxUploadSizeInBytes()) {
                $errors->add(new InvalidConfigurationDataError(FormDataProvider::ERROR_MAX_SIZE_ATTACHED_FILES, UploadQuotaType::FIELD_MAX_SIZE_ATTACHED_FILES));
            }
        }

        if (isset($data[UploadQuotaType::FIELD_MAX_SIZE_DOWNLOADABLE_FILE])) {
            $maxSizeDownloadableProduct = $data[UploadQuotaType::FIELD_MAX_SIZE_DOWNLOADABLE_FILE];
            if (!is_numeric($maxSizeDownloadableProduct) || $maxSizeDownloadableProduct < 0) {
                $errors->add(new InvalidConfigurationDataError(FormDataProvider::ERROR_NOT_NUMERIC_OR_LOWER_THAN_ZERO, UploadQuotaType::FIELD_MAX_SIZE_DOWNLOADABLE_FILE));
            }
        }

        if (isset($data[UploadQuotaType::FIELD_MAX_SIZE_PRODUCT_IMAGE])) {
            $maxSizeProductImage = $data[UploadQuotaType::FIELD_MAX_SIZE_PRODUCT_IMAGE];

            if (!is_numeric($maxSizeProductImage) || $maxSizeProductImage < 0) {
                $errors->add(new InvalidConfigurationDataError(FormDataProvider::ERROR_NOT_NUMERIC_OR_LOWER_THAN_ZERO, UploadQuotaType::FIELD_MAX_SIZE_PRODUCT_IMAGE));
            }
        }

        if (!$errors->isEmpty()) {
            throw new DataProviderException('Upload quota data is invalid', 0, null, $errors);
        }
    }

    private function convertBytes($value): int
    {
        if (is_numeric($value)) {
            return $value;
        } else {
            $value_length = strlen($value);
            $qty = (int) substr($value, 0, $value_length - 1);
            $unit = strtolower(substr($value, $value_length - 1));
            switch ($unit) {
                case 'k':
                    $qty *= 1024;

                    break;
                case 'm':
                    $qty *= 1048576;

                    break;
                case 'g':
                    $qty *= 1073741824;

                    break;
            }

            return $qty;
        }
    }
}
