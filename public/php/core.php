<?php
$list = schedule_verification();
    if($list == 0){
        $result = stop_movie();
        if($result==true){
            $msg = 'movie stopping.';
            logWrite($msg);
            exit;
        }
    }elseif($list == 1){
        logWrite('再生中です。');
        exit;
    }elseif(is_array($list)){
        $playlist = json_path($list);
        if(is_array($playlist)){
            $result = make_playlist($playlist);
            if($result == true){
                $result = playflag_chenge($list['id']);
                if($result == true){
                    $result = stop_movie();
                    if($result==true){
                        $result = list_play();
                        exit;
                    }
                }
            }
        }
    }
    logWrite($result);
    exit;

    // scheduleTableの確認
    function schedule_verification(){
        $dsn      = 'mysql:dbname=cms;host=localhost';
        $user     = 'root';
        $password = '';
        try{
            $dbh = new PDO($dsn, $user, $password);
            $query = "SELECT * FROM schedule WHERE delete_flag = 0 ORDER BY s_start ASC";
            $stmt   = $dbh->query($query);
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $today = date("Y-m-d H:i");
            $list = 0;
            foreach($result as $key => $value){
                $s_start = strtotime($value['s_start']);
                $s_end = strtotime($value['s_end']);
                if(strtotime($today) > $s_start && strtotime($today) < $s_end){
                    $list = $value;
                }
            }
            if($list!==0){
                if($list['play_flag']==0){
                    return $list;
                }else{
                    return 1;
                }
            }else{
                return $list;
            }
        }catch(PDOException $e){
            print("データベースの接続に失敗しました".$e->getMessage());
	        die();
        }
    }
    // pathの抽出
    function json_path($list){
        $json_list = json_decode($list['s_list']);
        $dsn      = 'mysql:dbname=cms;host=localhost';
        $user     = 'root';
        $password = '';
        try{
            $dbh = new PDO($dsn, $user, $password);
            $query = "SELECT * FROM contents WHERE delete_flag = 0";
            $stmt   = $dbh->query($query);
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            // file_nameの取得
            foreach($result as $key => $value){
                for($i=1; $i <= count((array)$json_list); $i++){
                    if($value['cid']==$json_list->{$i}){
                        $file = dirname( __FILE__ , 2)."\storage\\".$value['file_name'];
                        if(is_file($file)){
                            $path[] = $file;
                        }else{
                            echo 'fileが見当たりません';
                        }

                    }
                }
            }
            return $path;
        }catch(PDOException $e){
            print("データベースの接続に失敗しました".$e->getMessage());
	        die();
        }
    }
    // playlistの作成
    function make_playlist($path_list){
        $filename = dirname( __FILE__ ,2).'/bat/playlist.txt';
        // nullチェック
        if(!empty($path_list)){
            // fileの存在チェック
            if(file_exists($filename)) {
                // ディレクトリに書き込み可能か確認
                if( is_writable($filename)) {
                    $file = fopen($filename, "w");
                    foreach($path_list as $key => $value){
                        // ファイルへデータを書き込み
                        fwrite( $file, $value."\n");
                    }
                    // ファイルを閉じる
                    fclose($file);
                    return true;
                }
            }
        }
        return false;
    }
    // play_flagの変更
    function playflag_chenge($id){
        $dsn      = 'mysql:dbname=cms;host=localhost';
        $user     = 'root';
        $password = '';
        try{
            $dbh = new PDO($dsn, $user, $password);
            $query = "UPDATE schedule SET play_flag = 1 WHERE id = ?";
            $stmt  = $dbh->prepare($query);
	        $res   = $stmt->execute(array($id));
            if ($res){
                return true;
            }else{
                echo '{"error":"データの削除に失敗しました"}';
            }

        }catch(PDOException $e){
            print("データベースの接続に失敗しました".$e->getMessage());
	        die();
        }
    }
    // VLCを停止
    function stop_movie(){
        $file_path = dirname( __FILE__ , 2).'\bat\stop.bat';
        $tasklist = 'tasklist | find "vlc.exe"';
        if(file_exists($file_path)) {
            $bat = exec($file_path, $output, $return_var);
            if($return_var=='200'){
                exec($tasklist, $output, $return_var);
                // VLCが起動していなければ成功
                if($return_var == "1"){
                    return $msg = true;
                }else{
                    return $msg = 'stop.bat ERROR';
                }
            }else{
                return $msg = 'stop.bat ERROR';
            }
        }else{
            return $msg = 'stop.bat Not exist';
        }
    }

    // VLCを起動しplaylistの再生(bat)
    function list_play()
    {
        $command = dirname( __FILE__ , 2).'\bat\LoopPlayMovieFiles.bat';
        $option = " >/dev/null 2>&1 &";
        $tasklist = 'tasklist | find "vlc.exe"';
        if(file_exists($command)) {
            // 非同期呼び出し
            if (PHP_OS !== 'WIN32' && PHP_OS !== 'WINNT') {
                exec($command . $option, $output, $return_var);
            } else {
                $fp = popen('start "" '. $command, 'r');
                if($fp!==false){
                    $return_var = 0;
                    // commandが実行されたら実行中のプロセスを探す
                }else{
                    return $msg ='asynchronous ERROR';
                    exit;
                }
                pclose($fp);
            }
            if($return_var == '0'){
                sleep(1);
                exec($tasklist, $output, $return_var);
                // VLCが起動中であればsucceseを返す
                if($return_var == "0"){
                    return $msg = true;
                    exit;
                }else{
                    return $msg ='VLC Not working';
                    exit;
                }
            }else{
                return $msg ='LoopPlayMovieFiles.bat ERROR';
                exit;
            }
        }else{
            return $msg ='LoopPlayMovieFiles.bat Not exist';
            exit;
        }
    }

    // errorメッセージ
    function logWrite($msg){
        date_default_timezone_set('Asia/Tokyo');
        $content = date('Y年m月d日H時i分s秒');
        $filename = 'log/log.out';
        $data = $content.'_'.$msg;
        if(!empty($msg)){
            file_put_contents($filename,$data.PHP_EOL,FILE_APPEND | LOCK_EX);
        }else{
            file_put_contents($filename,'not message'.PHP_EOL,FILE_APPEND | LOCK_EX);
        }
    }


?>
