<div id="content">
<?php 

		$count = 1;
		$thumbnailBaseUrl = appendQueryString(CONFIG_URL_IMG_THUMBNAIL, makeQueryString(array('path')));
		foreach($fileList as $file)
		
		{
			?>
			<dl class="thumbnailListing" id="dl<?php echo $count; ?>">
				<dt id="dt<?php echo $count; ?>" class="<?php echo ($file['type'] == 'folder' && empty($file['file']) && empty($file['subdir'])?'folderEmpty':$file['cssClass']); ?>" class="<?php echo $file['cssClass']; ?>">
				<?php
					switch($file['cssClass'])
					{
						case 'filePicture':
								echo '<a id="thumbUrl' . $count . '" rel="thumbPhotos" href="' . $file['path'] . '">';
								echo '<img src="' . appendQueryString($thumbnailBaseUrl, 'path=' . $file['path']) . '" id="thumbImg' . $count . '"></a>' . "\n";
								break;
						case 'fileFlash':
						case 'fileVideo':
						case 'fileMusic':
							break;
						default:
							echo '&nbsp;';
					}
				?>
				
				</dt>
				<dd id="dd<?php echo $count; ?>" class="thumbnailListing_info"><span id="flag<?php echo $count; ?>" class="<?php echo $file['flag']; ?>">&nbsp;</span><input id="cb<?php echo $count; ?>" type="checkbox" name="check[]" <?php echo ($file['is_writable']?'':'disabled'); ?> class="radio" value="<?php echo $file['path']; ?>" />
				<a <?php echo ($file['cssClass']== 'filePicture'?'rel="orgImg"':''); ?> href="<?php echo $file['path']; ?>" title="<?php echo $file['name']; ?>" id="a<?php echo $count; ?>"><?php echo shortenFileName($file['name']); ?></a></dd>
				
			</dl>
			<?php
			$count++;
		}
?>
</div>