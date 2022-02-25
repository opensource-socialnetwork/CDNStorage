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
define('__CDNStorage__', ossn_route()->com . 'CDNStorage/');
ossn_register_class(array(
		'CDNStorage\Controller' => __CDNStorage__ . 'classes/Controller.php',
		'CDNStorage\Exception'  => __CDNStorage__ . 'classes/Exception.php',
));
/**
 * CDN Storage initialize
 *
 * @return void
 */
function cdn_storage_init(): void {
		ossn_add_hook('file', 'upload:cdn', 'cdn_storage_upload');
		ossn_register_callback('entity', 'before:delete', 'cdn_file_delete_checks');
		ossn_register_com_panel('CDNStorage', 'settings');
		if(ossn_isAdminLoggedin()) {
				ossn_register_action('admin/cdnstorage', __CDNStorage__ . 'actions/settings.php');
		}
}
/**
 * Handles the delete if local manifest file deleted delete from cdn
 *
 * @return void
 */
function cdn_file_delete_checks($hook, $type, $params): void {
		$file           = ossn_get_file($params['entity']);
		if($file && isset($file) && $file->isCDN()) {
				$path = str_replace('.cdn.manifest', '', $file->getParam('value'));
				$path = explode('/', $path);
				//removing file name from path and get the path that inititall set by setPath()
				$localpath = array_reverse($path);
				$fileName  = $localpath[0];
				unset($localpath[0]);

				$localpath      = array_reverse($localpath);
				$localpath      = implode('/', $localpath) . '/';
				$dir_local_path = "{$file->type}/{$file->owner_guid}/{$localpath}";
				
				$cdn = new \CDNStorage\Controller($dir_local_path, $file->guid);
				$cdn->delete($fileName);
		}
}
/**
 * Handles the upload to CDN
 *
 * @return array|bool
 */
function cdn_storage_upload($hook, $type, $return, $params): array | bool {
		$file          = $params['file'];
		$cdn           = new \CDNStorage\Controller($params['dir_local_path'], $params['fileguid']);
		$config        = $cdn->config();
		$cdn->mimeType = mime_content_type($file['tmp_name']);

		if($params['physicalFile'] == true) {
				$upload = $cdn->upload($file['tmp_name'], $params['newfilename'], 'public-read', true);
		} else {
				$upload = $cdn->upload($params['contents'], $params['newfilename'], 'public-read', false);
		}
		if($upload) {
				$path   = hash('sha1', $params['dir_local_path'] . $params['fileguid']) . '/';
				$URL    = parse_url($upload['ObjectURL']);
				$NoUri  = "{$URL['scheme']}://{$URL['host']}/";
				$result = array(
						'fullurl'  => $upload['ObjectURL'],
						'url'      => $NoUri,
						'success'  => true,
						'provier'  => $config['provider'],
						'filename' => $params['newfilename'],
						'fileguid' => $params['fileguid'],
						'path'     => $path,
				);
				return $result;
		}
		return false;
}
ossn_register_callback('ossn', 'init', 'cdn_storage_init');