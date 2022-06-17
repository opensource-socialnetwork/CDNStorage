<?php
	$site = new \OssnSite();
	$settings = $site->getSettings('cdnstorage.config');
	$settings = json_decode($settings, true);
?>
<div>
	<label><?php echo ossn_print('cdnstorage:bucketname');?></label>
    <input type="text" name="bucketname" value="<?php echo $settings['bucketname'];?>" />
</div>
<div>
	<label><?php echo ossn_print('cdnstorage:region');?></label>
    <input type="text" name="region" value="<?php echo $settings['region'];?>" />
</div>
<div>
	<label><?php echo ossn_print('cdnstorage:endpoint');?></label>
    <input type="text" name="endpoint" value="<?php echo $settings['endpoint'];?>"/>
</div>
<div>
	<label><?php echo ossn_print('cdnstorage:key');?></label>
    <input type="text" name="key" value="<?php echo $settings['key'];?>"/>
</div>
<div>
	<label><?php echo ossn_print('cdnstorage:secret');?></label>
    <input type="text" name="secret" value="<?php echo $settings['secret'];?>" />
</div>
<div>
	<label><?php echo ossn_print('cdnstorage:provider');?></label>
    <?php
		echo ossn_plugin_view('input/dropdown', array(
					'name' => 'provider',
					'value' => $settings['provider'],
					'options' => array(
						''	=> '',
						'DigitalOcean' => 'DigitalOcean Spaces',
						'AmazonS3' => 'Amazon S3',
					),
		));
	?>
</div>
<div>
	<label><?php echo ossn_print('cdnstorage:status');?></label>
    <?php
		echo ossn_plugin_view('input/dropdown', array(
					'name' => 'status',
					'value' => $site->getSettings('cdnstorage.status'),
					'options' => array(
						''	=> '',
						'enabled' => ossn_print('cdnstorage:enabled'),
						'disabled' => ossn_print('cdnstorage:disabled'),
					),
		));
	?>
</div>
<div>
	<input type="submit" class="btn btn-success btn-sm" value="<?php echo ossn_print('save');?>" />
</div>