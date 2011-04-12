<div id="content">
<table class="tableList" id="tableList" cellpadding="0" cellspacing="0" border="0">
					<thead>
						<tr>
							<th width="5"><a href="#" class="check_all" id="tickAll" title="<?php echo TIP_SELECT_ALL; ?>" onclick="checkAll(this);">&nbsp;</a></th>
							<th width="10" class="fileColumns">&nbsp;</th>
							<th class="docName"><?php echo LBL_NAME; ?></th>
							<th  width="70" class="fileColumns"><?php echo LBL_SIZE; ?></th>
							<th class="fileColumns"><?php echo LBL_MODIFIED; ?></th>
						</tr>
					</thead>
					<tbody id="fileList">
						<?php
							$count = 1;
							$css = "";
							//list all documents (files and folders) under this current folder, 
							//<?php echo appendQueryString(appendQueryString(CONFIG_URL_FILEnIMAGE_MANAGER, "path=" . $file['path']), makeQueryString(array('path'))); 
							foreach($fileList as $file)
							{
								$css = ($css == "" || $css == "even"?"odd":"even");
								$strDisabled = ($file['is_writable']?"":" disabled");
								$strClass = ($file['is_writable']?"left":" leftDisabled");
								if($file['type'] == 'file')
								{

								?>
								<tr class="<?php echo $css; ?>" id="row<?php echo $count; ?>"  >
									<td align="center" id="tdz<?php echo $count; ?>"><span id="flag<?php echo $count; ?>" class="<?php echo $file['flag']; ?>">&nbsp;</span><input type="checkbox"  name="check[]" id="cb<?php echo $count; ?>" value="<?php echo $file['path']; ?>" <?php echo $strDisabled; ?> />
										</td>
									<td align="center" class="fileColumns" id="tdst<?php echo $count; ?>">&nbsp;<a id="a<?php echo $count; ?>" href="<?php echo $file['path']; ?>" target="_blank"><span class="<?php echo $file['cssClass']; ?>">&nbsp;</span></a></td>
									<td class="<?php echo $strClass; ?> docName"  id="tdnd<?php echo $count; ?>"><?php echo $file['name']; ?></td>
									<td class="docInfo" id="tdrd<?php echo $count; ?>"><?php echo transformFileSize($file['size']); ?></td>
									<td class="docInfo" id="tdth<?php echo $count; ?>"><?php echo @date(DATE_TIME_FORMAT,$file['mtime']); ?></td>
								</tr>
								<?php
								}else
								{
									?>
									<tr class="<?php echo $css; ?>" id="row<?php echo $count; ?>" >
										<td align="center" id="tdz<?php echo $count; ?>"><span id="flag<?php echo $count; ?>" class="<?php echo $file['flag']; ?>">&nbsp;</span><input type="checkbox" name="check[]" id="cb<?php echo $count; ?>" value="<?php echo $file['path']; ?>" <?php echo $strDisabled; ?>/>
										</td>
										<td  lign="center" class="fileColumns" id="tdst<?php echo $count; ?>">&nbsp;<a id="a<?php echo $count; ?>" href="<?php echo $file['path']; ?>" <?php echo $file['cssClass'] == 'filePicture'?'rel="ajaxPhotos"':''; ?>  ><span class="<?php echo ($file['file']||$file['subdir']?$file['cssClass']:"folderEmpty"); ?>">&nbsp;</span></a></td>
										<td class="<?php echo $strClass; ?> docName" id="tdnd<?php echo $count; ?>"><?php echo $file['name']; ?></td>
										<td class="docInfo" id="tdrd<?php echo $count; ?>">&nbsp;</td>
										<td class="docInfo" id="tdth<?php echo $count; ?>"><?php echo @date(DATE_TIME_FORMAT,$file['mtime']); ?></td>
									</tr>
									<?php
								}
								$count++;
							}
						?>	
					</tbody>
				</table>
</div>