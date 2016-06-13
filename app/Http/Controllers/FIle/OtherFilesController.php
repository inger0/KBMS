<?php namespace  App\Http\Controllers\File;

    use App\Http\Controllers\Controller;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\DB;
    /**
     * Created by PhpStorm.
     * User: 98259
     * Date: 2016/6/5 0005
     * Time: 15:39
     */
    date_default_timezone_set("Asia/Shanghai");
    class OtherFilesController extends Controller
    {
        /*
         * 上传文件
         * */
        public function upload(Request $request){
            if(session('id')){
                $uid = session('id');
                $file = $_FILES['mof'];
                $succeed = $_POST['succeed'];
                $myname = $_POST['myname'];
                $type = trim(strrchr($_POST['test'], '.'),'.');
                if($succeed != '1'){
                    if($file['error'] == 0){
                        if(!file_exists('./uploads/upload.'.$type)){
                            if(!move_uploaded_file($file['tmp_name'],'./uploads/UP.'.$myname.".".$type)){
                                return response('failed');
                            }else{
                                return response('ok');
                            }
                        }else{
                            $content=file_get_contents($file['tmp_name']);
                            if (!file_put_contents('./uploads/upload.'.$myname.".".$type, $content,FILE_APPEND)) {
                                return response('failed');
                            }else{
                                return response('ok');
                            }
                        }
                    }else{
                        return response('failed');
                    }
                }else{
                    $filepath = $_POST['myname']?  "./uploads/UP.".$myname = $_POST['myname'].$type : "" ;
                    $filehead = $_POST['filename']? $_POST['filename']: "" ;
                    $filesize = $_POST['filesize']?  $_POST['filesize']: "" ;
                    $filefolder = $_POST['filefolder']? $_POST['filefolder']: "";
                    $createtime = $updatetime =  date("Y-M-D H:i:sa");
                    $sql  = "INSERT INTO files (filehead,)";

                    return response("succeed");
                }
            }else{
                return response("-1");
            }

        }

        public function MyType($type){
            $type = strtolower($type);
            $photo = array("bmp","pcx","tiff","gif","jpeg","tga",
                        "exif","fpx","svg","cdr","pcd","dxf","ufo",
                        "eps","ai","png","hdri","raw");
            $music = array("");
            $video = array("mpeg","mpg","dat","avi","mov","asf",
                        "wmv","rmvb","flv","f4v","mp4","3gp","amv");

            if($type == "jpg" || $type == "jpeg" ){

            }else if(""){

            }
        }
        /*
         *删除文件
         * */
        public function delete(Request $request){
            if(session('id')){
                $uid = session('id');
                $fileid= $request->input('fileid')? $request->input('fileid') : "";
                if($uid && $fileid){
                    $sql01 = "SELECT * FROM files WHERE  fileid=? and uid=?";
                    $res01 =  DB::select($sql01,[$fileid,$uid]);
                    if($res01){
                        $result  = @unlink('./upload/'.$res01[0]->filehead);
                        if(!$result){
                            $sql02 = "DELETE FROM files WHERE fileid=? and uid=?";
                            $res02 = DB::delete($sql02,[$fileid,$uid]);
                            if ($res02){
                                return response("1");
                            }else{
                                return response("-5");
                            }
                        }else{
                            return response("-4");
                        }
                    }else{
                        return response("-3");
                    }
                }else{
                    return response("-2");
                }
            }else{
                return response("-1");
            }
        }
        /*
         * 给文件 重命名
         *
         * */
        public function rename(Request $request){
            if(session('id')) {
                $uid = session('id');
                $fileid = $request->input('fileid') ? $request->input('fileid') : "";
                $filename = $request->input('filename') ? $request->input('filename') : "";
                $updatetime = date("Y-M-D H:i:sa");
                if($uid && $fileid && $filename){
                    $sql = "UPDATE files SET filehead=?,updatetime=? WHERE fid=? ";
                    $res = DB::update($sql,[$filename,$updatetime,$fileid]);
                    return $res? 1 : -3;
                }else{
                    return response("-2");
                }
            }else{
                return response("-1");
            }
        }

        /*
        *
        * 文件夹移动到
        *
        * */
        public function Filemoveto(){

        }

        /*
         * 文件夹复制到
         * */
        public function Filecopy(){

        }
        /*
         * 创建文件夹
         * */
        public function createfolder(Request $request){
            if(session('id')){
                $uid = session('id');
                $foldername = $request->input('foldername')? $request->input('foldername') : "";
                $folderpreid = $request->input('folderpreid')? $request->input('folderpreid') : "";
                $foldergrade = $request->input('foldergrade')? $request->input('foldergrade') : "";
                $foldertype = $request->input('foldertype')? $request->input('foldertype') : "";
                $updatetime = date("Y-m-d H:i:s");
                if($uid && $foldername && $folderpreid != "" && $foldergrade && $foldertype){
                    $sql01 = "SELECT * from folders WHERE folname=? and uid=? and grade=?";
                    $res01 = DB::select($sql01,[$foldername,$uid,$foldergrade]);
                    if(!$res01){
                        /*
                         * 查看最大的索引
                         * */
                        $sql02 = "SELECT MAX(folid) as max from folders";
                        $res02 = DB::select($sql02);
                        $folid = $res02[0]->max+1;
                        if($folderpreid == -1){
                            /** @var TYPE_NAME $sql */
                            $sql = "INSERT INTO folders(folid,folname,folpreid,grade,type,updatetime,uid) VALUES (?,?,?,?,?,?,?)";
                            /** @var TYPE_NAME $res */
                            $res = DB::insert($sql,[$folid,$foldername,$folid,$foldergrade,$foldertype,$updatetime,$uid]);
                            return $res ? 1 : -4;
                        }else{
                            /**
                             * 插入到 folders 表格中
                             */
                            $sql = "INSERT INTO folders(folid,folname,folpreid,grade,type,updatetime,uid) VALUES (?,?,?,?,?,?,?)";
                            /** @var TYPE_NAME $res */
                            $res = DB::insert($sql,[$folid,$foldername,$folderpreid,$foldergrade,$foldertype,$updatetime,$uid]);
                            return $res ? 1 : -4;
                        }
                    }else{
                        return response("-3");
                    }
                }else{
                    return response("-2");
                }
            }else{
                return response("-1");
            }
        }

        /*
         * 删除文件夹
         * */
        /**
         * @param Request $request
         * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
         */
        public function deletefolder( Request $request){
            $uid = session('id');
            $folderid = $request->input('folderid')? $request->input('folderid') : "";
            if($uid && $folderid ){
                $arr = "(";
                for($i =1;$i <= count($folderid);$i++){
                    $arr .= $folderid[i];
                    if($i != count($folderid)){
                        $arr .= ",";
                    }
                }
                $arr .= ")";
                $sql = "DELETE FROM folders where folid in ".$arr." and uid=?";
                return $sql;
              /*  $res = DB::delete($sql,[$folderid,$uid]);
                if($res){
                    return response("1");
                }else{
                    return response("-3");
                }*/
            }else{
                return response("-1");
            }
        }
        /*
         *重命名文件夹
         * */
        public function renameFolder(Request $request){
            if(session('id')){
                $uid = session('id');
                $foldername = $request->input('foldername')? $request->input('foldername') : "";
                $folderid = $request->input('folderid')? $request->input('folderid') : "";
                $updatetime = date("Y-m-d H:i:sa");
                if($uid && $foldername && $folderid){
                    $sql = "UPDATE folders SET folname=?,updatetime=? WHERE $folderid=?";
                    $res = DB::update($sql,[$foldername,$updatetime,$folderid]);
                    return $res? 1 : -3;
                }else{
                    return response("-2");
                }
            }else{
                return response("-1");
            }
        }
        /*
         *
         * 文件夹移动到
         *
         * */
        public function Folmoveto(){

        }

        /*
         * 文件夹复制到
         * */
        public function Folcopy(){

        }

}