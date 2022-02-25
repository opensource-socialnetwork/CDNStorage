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
namespace CDNStorage;
class Controller {
		/**
		 * Config
		 *
		 * @return array|bool
		 */
		public static function config(): array | bool {
				$site = new \OssnSite();
				$settings = $site->getSettings('cdnstorage.config');			
				if(!$settings) {
						return false;
				}
				$settings = json_decode($settings, true);
				return $settings;
		}
		
		/**
		 * Setup the file details
		 *
		 * @param string $dir_local_path local sotrage path
		 * @param int	 $fileguid File guid
		 *
		 * @return void
		 */
		public function __construct(string $dir_local_path, $fileguid) {
				require_once __CDNStorage__ . 'vendors/aws/aws-autoloader.php';

				$this->dir_local_path = hash('sha1', $dir_local_path . $fileguid) . '/';

				$settings = $this->config();
				if(empty($settings['key']) || empty($settings['secret'])) {
						throw new \CDNStorage\Exception('Invalid Settings');
				}
				$this->bucketName = $settings['bucketname'];
				$this->client     = new \Aws\S3\S3Client(array(
						'version'     => 'latest',
						'region'      => $settings['region'],
						'endpoint'    => $settings['endpoint'],
						'credentials' => array(
								'key'    => $settings['key'],
								'secret' => $settings['secret'],
						),
				));
		}
		
		/**
		 * Delete the file path (entire directory)
		 * Because we store files based on fileguid
		 *
		 * @param string $path name of file
		 *
		 * @return void
		 */
		public function delete($path) {
				if(!empty($path)) {
						$bucket = $this->bucketName;
						try {
								$this->client->deleteMatchingObjects($bucket, $this->dir_local_path . $path);
						} catch (DeleteMultipleObjectsException $exception) {
								return false;
						}
						return true;
				}
		}
		/**
		 * Upload the file
		 *
		 * @parma string $localfile A full path for temporary file
		 * @param string $path name of file
		 * @param string $acl  Accessiblity public-read means sites users can view the file
		 * @param bool	 $physicalFile If the file path is physical file or a dynaic generated file as string
		 *
		 * @return void
		 */
		public function upload($localfile, $path, $acl = 'public-read', $physicalFile = true) {
				$bucket = $this->bucketName;
				if($physicalFile) {
						$source = fopen($localfile, 'rb');
				} else {
						$source = $localfile;
				}
				$uploader = new \Aws\S3\ObjectUploader($this->client, $bucket, $this->dir_local_path . $path, $source, $acl, array(
						'params' => array(
								'CacheControl' => 'max-age=604800',
								'ContentType'  => $this->mimeType,
						),
				));
				$result = $uploader->upload();
				if($result['@metadata']['statusCode'] == '200') {
						return $result;
				}
				return false;
		}
}