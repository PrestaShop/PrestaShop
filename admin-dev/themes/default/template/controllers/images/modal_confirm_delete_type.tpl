{**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 *}
<div class="modal-body">
  <div class="form-group">
    {l
      s="If you delete this image format, the theme won't be able to use it anymore. This will result in a degraded experience on your front office."
      d="Admin.Design.Notification"
    }
  </div>

  <div class="modal-checkbox">
    <input type="checkbox" id="delete_linked_images" name="delete">
    <label for="delete_linked_images">{l s="Delete the images linked to this image setting" d="Admin.Design.Notification"}</label>
  </div>
</div>
