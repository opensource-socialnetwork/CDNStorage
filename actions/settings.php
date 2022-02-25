<?php
/**
 * Open Source Social Network
 *
 * @package   CDN Storage
 * @author    Engr.Syed Arsalan Hussain Shah
 * @copyright (C) Engr.Syed Arsalan Hussain Shah
 * @license   Open Source Social Network License (OSSN LICENSE)  http://www.opensource-socialnetwork.org/licence
 * @link      https://www.opensource-socialnetwork.org/
 */
 $options = array(
		'bucketname' => input('bucketname'),
		'region' => input('region'),
		'endpoint' => input('endpoint'),
		'key' => input('key'),
		'secret' => input('secret'),
		'provider' => input('provider'),
		'status' => input('status')
 );
 foreach($options as $key => $item){
		if(empty($item)){
				ossn_trigger_message(ossn_print('cdnstorage:invalid:field', array($key)), 'error');
				redirect(REF);
		}
 }
 $status = $options['status'];
 
 unset($options['status']);
 $options = json_encode($options);
 
 $setting = new OssnSite;
 if($setting->setSetting('cdnstorage.config', $options) && $setting->setSetting('cdnstorage.status', $status)){
				ossn_trigger_message(ossn_print('cdnstorage:settings:saved'));
				redirect(REF);
 }
ossn_trigger_message(ossn_print('cdnstorage:settings:error'));
redirect(REF);