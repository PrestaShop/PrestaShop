<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Title;

use PrestaShop\PrestaShop\Adapter\Image\Uploader\TitleImageUploader;
use PrestaShop\PrestaShop\Adapter\Title\Repository\TitleRepository;

class AbstractTitleHandler
{
    /**
     * @var TitleRepository
     */
    protected $titleRepository;

    /**
     * @var TitleImageUploader
     */
    protected $titleImageUploader;

    /**
     * @param TitleRepository $titleRepository
     * @param TitleImageUploader $titleImageUploader
     */
    public function __construct(TitleRepository $titleRepository, TitleImageUploader $titleImageUploader)
    {
        $this->titleRepository = $titleRepository;
        $this->titleImageUploader = $titleImageUploader;
    }
}
