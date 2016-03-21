<?php

/*
 * 编辑器上传控制器
 */

class UploadAction extends Action {

    //put your code here
    function fileManager() 
    {
        $dir = $this->_get('dirname');
        //图片扩展名
        $ext_arr = array('gif', 'jpg', 'jpeg', 'png', 'bmp');
        //目录名
        $dir_name = empty($dir) ? 'Upload' : trim($dir);
        if (!in_array($dir_name, array('Upload', 'image', 'flash', 'media', 'file','Sysicon'))) {
            echo "Invalid Directory name.";
            exit;
        }
       
        $php_url = dirname($_SERVER['PHP_SELF']) . '/';

        //根目录路径，可以指定绝对路径，比如 /var/www/attached/
        $root_path =  ROOT_PATH.'/Public/'.$dir_name;
        //根目录URL，可以指定绝对路径，比如 http://www.yoursite.com/attached/
        $root_url =  C('SITEURL').'/Public/'.$dir_name.'/';
        //根据path参数，设置各路径和URL
        if (empty($_GET['path'])) {
                $current_path = realpath($root_path) . '/';
                $current_url = $root_url;
                $current_dir_path = '';
                $moveup_dir_path = '';
        } else {
                $current_path = realpath($root_path) . '/' . $_GET['path'];
                $current_url = $root_url . $_GET['path'];
                $current_dir_path = $_GET['path'];
                $moveup_dir_path = preg_replace('/(.*?)[^\/]+\/$/', '$1', $current_dir_path);
        }
      
        //echo realpath($root_path);
        //排序形式，name or size or type
        $order = empty($_GET['order']) ? 'name' : strtolower($_GET['order']);

        //不允许使用..移动到上一级目录
        if (preg_match('/\.\./', $current_path)) {
            echo 'Access is not allowed.';
            exit;
        }
        //最后一个字符不是/
        if (!preg_match('/\/$/', $current_path)) {
            echo 'Parameter is not valid.';
            exit;
        }
        //目录不存在或不是目录
        if (!file_exists($current_path) || !is_dir($current_path)) {
            echo 'Directory does not exist.';
            exit;
        }
        
        //遍历目录取得文件信息
        $file_list = array();
        if ($handle = opendir($current_path)) {
            $i = 0;
            while (false !== ($filename = readdir($handle))) {
                if ($filename{0} == '.')
                    continue;
                $file = $current_path . $filename;
                if (is_dir($file)) {
                    $file_list[$i]['is_dir'] = true; //是否文件夹
                    $file_list[$i]['has_file'] = (count(scandir($file)) > 2); //文件夹是否包含文件
                    $file_list[$i]['filesize'] = 0; //文件大小
                    $file_list[$i]['is_photo'] = false; //是否图片
                    $file_list[$i]['filetype'] = ''; //文件类别，用扩展名判断
                } else {
                    $file_list[$i]['is_dir'] = false;
                    $file_list[$i]['has_file'] = false;
                    $file_list[$i]['filesize'] = filesize($file);
                    $file_list[$i]['dir_path'] = '';
                    $file_ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                    $file_list[$i]['is_photo'] = in_array($file_ext, $ext_arr);
                    $file_list[$i]['filetype'] = $file_ext;
                }
                $file_list[$i]['filename'] = $filename; //文件名，包含扩展名
                $file_list[$i]['datetime'] = date('Y-m-d H:i:s', filemtime($file)); //文件最后修改时间
                $i++;
            }
            closedir($handle);
        }
        usort($file_list, 'cmp_func');
        $result = array();
        //相对于根目录的上一级目录
        $result['moveup_dir_path'] = $moveup_dir_path;
        //相对于根目录的当前目录
        $result['current_dir_path'] = $current_dir_path;
        //当前目录的URL
        $result['current_url'] = $current_url;
        //文件数
        $result['total_count'] = count($file_list);
        //文件列表数组
        $result['file_list'] = $file_list;
     
        //输出JSON字符串
        header('Content-type: application/json; charset=UTF-8');
        import('@.Extend.Services_JSON');
        $json = new Services_JSON();
        echo $json->encode($result);
    }
    
    function uploadFile()
    {
        $ext_arr = C('UPLOADFILETYPE');
        //检查目录名
        $dir_name = empty($_GET['dir']) ? 'image' : trim($_GET['dir']);
        $valid_md5 =  empty($_GET['validhash']) ? '' : trim($_GET['validhash']);
        if (empty($ext_arr[$dir_name])) {
            $this->alert("目录名不正确。");
        }
        
        import('@.Extend.UploadFile');
        $upload = new UploadFile();// 实例化上传类
        $upload->maxSize  = C('FILEMAXSIZE') ;// 设置附件上传大小
        $upload->allowExts  = $ext_arr[$dir_name];// 设置附件上传类型
        $upload->savePath =  C('UPLOADFILEDIR').$dir_name.'/';// 设置附件上传目录 为了避免表单页面和上传页面url类型不统一  上传类里面做检查
        $upload->thumb = empty($_GET['imagethumb']) ? C('IMAGETHUMB') : $_GET['imagethumb'];; //开启缩略图
        $upload->thumbPrefix = C('THUMBPREFIX'); //缩略图保存前缀
        if(!$upload->upload()) // 上传错误提示错误信息
        {
            $error = $upload->getErrorMsg();
            $this->alert($error);
        }else{// 上传成功 获取上传文件信息
            $sucInfo =  $upload->getUploadFileInfo();
            //上传完进行图片入库处理
            $fileinfo = D('Fileinfo');
            C('TOKEN_ON',false); //关闭表单令牌
            foreach($sucInfo as $k=>$v)
            {
                $sucInfo[$k]['thumb_name'] = $upload->thumbPrefix.$v['savename'];
                $sucInfo[$k]['save_dir'] = $dir_name;
                $sucInfo[$k]['save_path'] = $upload->savePath;
                $sucInfo[$k]['old_name'] = $v['name'];
                $sucInfo[$k]['name'] = $v['savename'];
                $sucInfo[$k]['valid_md5'] = $valid_md5;
                if($fileinfo->create($sucInfo[$k]))
                {
                    $fileinfo->add();
                    
                    $file_url = $upload->savePath . $v['savename'];
                    @chmod($v['savepath'].$v['savename'], 0644);
                    @chmod($v['savepath'].$v['thumb_name'], 0644);
                    header('Content-type: text/html; charset=UTF-8');
                    import('@.Extend.Services_JSON');
                    $json = new Services_JSON();
                    echo $json->encode(array('error' => 0, 'url' => $file_url));
                    exit;
                }
            }
            
            
        }
     
    }
    function alert($msg) {
        header('Content-type: text/html; charset=UTF-8');
        import('@.Extend.Services_JSON');
        $json = new Services_JSON();
        echo $json->encode(array('error' => 1, 'message' => $msg));
        exit;
    }
    //排序
    function cmp_func($a, $b) {
        global $order;
        if ($a['is_dir'] && !$b['is_dir']) {
            return -1;
        } else if (!$a['is_dir'] && $b['is_dir']) {
            return 1;
        } else {
            if ($order == 'size') {
                if ($a['filesize'] > $b['filesize']) {
                    return 1;
                } else if ($a['filesize'] < $b['filesize']) {
                    return -1;
                } else {
                    return 0;
                }
            } else if ($order == 'type') {
                return strcmp($a['filetype'], $b['filetype']);
            } else {
                return strcmp($a['filename'], $b['filename']);
            }
        }
    }

}
