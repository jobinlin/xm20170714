<?php 
// +----------------------------------------------------------------------
// | Fanwe 方维o2o商业系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(97139915@qq.com)
// +----------------------------------------------------------------------

//同步本地文件至阿里oss
define("FILE_PATH", __DIR__); //文件目录，空为根目录

$system_init_file = dirname(__DIR__).'/system/system_init.php';
require_once $system_init_file;

set_time_limit(20);
error_reporting(ALL);

/**
* ali_oss_syn
*/
class AliOssSyn
{
	private $local_cfg_file;

	private $imgRoot;

	private $bucket;

	private $imgDirs;

	private $delSuccNum = 0;

	private $delErrNum = 0;

	private $ossHostname;

	private $successImgNum = 0;

	private $errorImgNum = 0;

	private $ossDirectory = '';

	public function __construct($options = array())
	{
		$this->imgRoot = dirname(__DIR__).'/';

		if (isset($options['imgDirs'])) {
			$this->imgDirs = $options['imgDirs'];
		}

		if (isset($options['ossHostname'])) {
			$this->ossHostname = $options['ossHostname'];
		}
		if (!empty($options['bucket'])) {
			$this->bucket = $options['bucket'];
		} else {
			$this->bucket = $GLOBALS['distribution_cfg']['OSS_BUCKET_NAME'];
		}

		if (isset($options['ossDirectory'])) {
			$this->ossDirectory = $options['ossDirectory'];
		}
		
	}

	/**
	 * 同步图片方法 
	 * @return  
	 */
	public function syn()
	{
		if (!empty($this->imgDirs)) {
			// 先删除子目录裁剪后的图片
			/*foreach ($this->imgDirs as $dir) {
				$absDir = $this->imgRoot.$dir;
				$this->delImg($absDir);
			}*/
			
			require_once APP_ROOT_PATH."system/alioss/sdk.class.php";
			$oss_sdk_service = new ALIOSS(NULL, NULL, $this->ossHostname);
			//设置是否打开curl调试模式
			$oss_sdk_service->set_debug_mode(true);
			// 设置每张图片同步的最大次数
			$oss_sdk_service->set_max_retries(1);

			foreach ($this->imgDirs as $dir) {
				$absDir = $this->imgRoot.$dir;
				$this->synImg($oss_sdk_service, $this->bucket, $absDir, $this->ossDirectory);
			}

		}
	}

	/**
	 * 删除本地图片
	 * @param  string $absDir 
	 * @return          
	 */
	public function delImg($absDir)
	{
		$fi = new FilesystemIterator($absDir, FilesystemIterator::SKIP_DOTS);
		foreach ($fi as $file) {
			if ($file->isDir()) {
				$this->delImg($file->getPathname());
			} else {
				$pattern = '/\_\d+x\d+\.(jpg|png|gif|jpeg)$/i';
				if (preg_match($pattern, $file->getFilename())) {
					// echo '--'.$file->getFilename()."\n";
					if (unlink($file->getPathname()) === true) {
						$this->delSuccNum++;
					} else {
						$this->delErrNum++;
					}
				} else {
					// echo $file->getFilename();
				}
			}
		}
	}

	/**
	 * 同步oss图片方法
	 * @param  object $service      sdk类
	 * @param  string $bucket       
	 * @param  本地图片路径 $path         
	 * @param  string $ossDirectory oss二级目录
	 * @return                
	 */
	public function synImg($service, $bucket, $path, $ossDirectory = '')
	{
		if ( $dir = opendir( $path ) ) {
			while ( $file = readdir( $dir ) ) {
				$check = is_dir( $path. $file );
				if ( !$check  && preg_match('/\.(jpg|png|gif|jpeg)$/i', $file)) {
					if(!preg_match("/_(\d+)x(\d+)/i",$file)) {
						//同步
						$file_dir = str_replace(APP_ROOT_PATH, "", $path);	
						$object = $ossDirectory.$file_dir.$file;
						$file_path = $path. $file;
						if (in_array($file_path, $GLOBALS['synedFileArray'])) {
							continue;
						}
						logger::write('当前文件名--'.$file_path);
						$response = $service->upload_file_by_file($bucket,$object,$file_path);
						if ($response->isOk()) {
						    $this->successImgNum++;
							file_put_contents($GLOBALS['synedFilePath'], '|'.$file_path, FILE_APPEND|LOCK_EX);
						} else {
						    $this->errorImgNum++;
                        }
					}
				} else {
					if($file!='.'&&$file!='..') {
						$this->synImg($service, $bucket, $path.$file."/");
					}
				}
			}
			closedir( $dir );
		} else {
			echo 'not readable path: '.$path;
		}
	}

	// 单张图片测试
	public function synOneImg()
    {
        require_once APP_ROOT_PATH."system/alioss/sdk.class.php";
        $oss_sdk_service = new ALIOSS(NULL, NULL, $this->ossHostname);
        //设置是否打开curl调试模式
        $oss_sdk_service->set_debug_mode(true);
        $oss_sdk_service->set_max_retries(1);
        // 本地图片目录
        $path = '/fanwe/www/testo2onew.fanwe.net/public/avatar/000/00/02/';
        // 图片名
        $file = '03virtual_avatar_middle.jpg';
        // oss对应路径名
        $object = 'testo2onew/public/avatar/000/00/02/'.$file;
        // 本地图片完整路径
        $file_path = $path. $file;
        $response = $oss_sdk_service->upload_file_by_file('o2ofanwenet',$object,
            $file_path);
        logger::write(serialize($response));
    }

	public function getSuccSynImgNum()
    {
        return $this->successImgNum;
    }

    public function getErrSynImgNum()
    {
        return $this->errorImgNum;
    }
}


// $synedFilePath = './synedFilename.txt';
// 同步过的图片名写入文件，下次不再重复同步，
$synedFilePath = APP_ROOT_PATH.'public/synedFilename.txt';

$synedFileContent = file_get_contents($synedFilePath);

$synedFileArray = array();
if ($synedFileContent !== false) {
	$synedFileArray = explode('|', $synedFileContent);
}


$options = array(
	// 要同步的本地图片路径 不包含网站跟目录，以斜杠结尾
	'imgDirs' => array(
		// 'public/attachment/',
	),
	// OSS服务地址 不能有路径参数
	'ossHostname' => '',
	'bucket' => '',
	// OSS二级目录 (如果有 已斜杠结尾 如 testo2onew/)
	'ossDirectory' => '',
);

logger::write( '开始同步.....' );
$obj = new AliOssSyn($options);
$obj->syn();
// $obj->synOneImg();
logger::write('同步成功的图片数量：'.$obj->getSuccSynImgNum());
logger::write('同步失败的图片数量：'.$obj->getErrSynImgNum());
logger::write( '同步结束' );
