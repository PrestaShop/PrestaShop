<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
class AdminKernel extends AppKernel
{
    public const APP_ID = 'admin';

    public function getAppId(): string
    {
        return self::APP_ID;
    }
}
