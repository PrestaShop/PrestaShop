<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Import\Configuration;

use Symfony\Component\HttpFoundation\Request;

/**
 * Class ImportRuntimeConfigFactory is responsible for building import runtime config.
 */
final class ImportRuntimeConfigFactory implements ImportRuntimeConfigFactoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function buildFromRequest(Request $request)
    {
        $sharedData = $request->request->get('crossStepsVars', '');

        return new ImportRuntimeConfig(
            $request->request->getBoolean('validateOnly'),
            $request->request->getInt('offset'),
            $request->request->getInt('limit'),
            json_decode($sharedData, true),
            $request->request->all('type_value')
        );
    }
}
