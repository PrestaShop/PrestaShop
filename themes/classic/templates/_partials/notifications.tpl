{**
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
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2016 PrestaShop SA
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 *}

<aside id="notifications">
  <div class="container">
    {if $notifications.error}
      <article class="alert alert-danger" role="alert" data-alert="danger">
        <ul>
          {foreach $notifications.error as $notif}
            <li>{$notif}</li>
          {/foreach}
        </ul>
      </article>
    {/if}

    {if $notifications.warning}
      <article class="alert alert-warning" role="alert" data-alert="warning">
        <ul>
          {foreach $notifications.warning as $notif}
            <li>{$notif}</li>
          {/foreach}
        </ul>
      </article>
    {/if}

    {if $notifications.success}
      <article class="alert alert-success" role="alert" data-alert="success">
        <ul>
          {foreach $notifications.success as $notif}
            <li>{$notif}</li>
          {/foreach}
        </ul>
      </article>
    {/if}

    {if $notifications.info}
      <article class="alert alert-info" role="alert" data-alert="info">
        <ul>
          {foreach $notifications.info as $notif}
            <li>{$notif}</li>
          {/foreach}
        </ul>
      </article>
    {/if}
  </div>
</aside>
