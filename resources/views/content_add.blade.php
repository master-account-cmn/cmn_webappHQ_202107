@extends('layouts.app')
@section('content')

<ol class="breadcrumb" itemscope itemtype="https://schema.org/BreadcrumbList">
    <!-- 1つめ -->
    <li itemprop="itemListElement" itemscope
        itemtype="https://schema.org/ListItem">
      <a itemprop="item" href="/top">
          <span itemprop="name">トップ</span>
      </a>
      <meta itemprop="position" content="1" />
    </li>

    <!-- 2つめ -->
    <li itemprop="itemListElement" itemscope
        itemtype="https://schema.org/ListItem">
      <a itemprop="item" href="/contents">
          <span itemprop="name">コンテンツ一覧</span>
      </a>
      <meta itemprop="position" content="2" />
    </li>

    <!-- 3つめ -->
    <li itemprop="itemListElement" itemscope
        itemtype="https://schema.org/ListItem">
      <a itemprop="item" href="/create/content">
          <span itemprop="name">コンテンツ登録編集</span>
      </a>
      <meta itemprop="position" content="3" />
    </li>
  </ol>

  <form method="post" id="c_form" enctype="multipart/form-data" action="updatecontents.php">
      <table>

          <tr>
              <th>ID</th>
              <td>自動発番</td>
          </tr>

          <tr>
              <th>コンテンツID</th>
              <td>自動発番</td>
          </tr>

          <tr>
              <th>コンテンツ名</th>
              <td>
                  <input type="text" class="len400" name="name" id="name" value="" />
              </td>
          </tr>

          <tr>
              <th>表示名</th>
              <td>
                  <input type="text" class="len400" name="display_name" id="display_name" value="" />
              </td>
          </tr>

          <tr>
              <th>ファイルを選択</th>
              <td>
                  <input type="file" class="file" id="content" name="content" accept="image/jpeg, image/png, .mp4" required onchange="getFileData();" />
              </td>
          </tr>

          <tr>
              <th>ファイルタイプ</th>
              <td>
                  <label id="file_type"></label>
                  <input type="hidden" name="file_type" id="file_type_h" value="" />
              </td>
          </tr>

          <tr>
              <th>ファイルサイズ</th>
              <td>
                  <label id="file_size"></label>
                  <input type="hidden" name="file_size" id="file_size_h" value="" />
              </td>
          </tr>

          <tr>
              <th>コンテンツタイプ</th>
              <td>
                  <label id="content_type"></label>
                  <input type="hidden" name="content_type" id="content_type_h" value="" />
              </td>
          </tr>

          <tr>
              <th>表示時間</th>
              <td>
                  <label id="content_time"></label>
                  <input type="hidden" name="content_time" id="content_time_h" value="" />
              </td>
          </tr>


          <tr>
              <th>プレビュー</th>
              <td>
                  <div id="preview"></div>
              </td>
          </tr>

      </table>


      <div style="width:100px;height:50px;margin:auto;">
          <input type="button" value="登録" onclick="updateContent();" />
      </div>
  </form>

  <script>

    function updateContent(){

        if (!$("#name").val() || !$("#display_name").val()){
            alert("コンテンツ名またはタイトルが未入力です");
            return false;
        }
        if($('#content').val()==''){
            alert('ファイルが選択されていません。');
            return false;
        }

        // フォームデータを取得
        var formdata = new FormData($('#c_form').get(0));

        // POSTでアップロード
        $.ajax({
            url  : "/store/content",
            type : "POST",
            data : formdata,
            cache       : false,
            contentType : false,
            processData : false,
            dataType    : "html",
            headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
        })
        .done(function(data){
            console.log(data);
            alert("登録しました");
            //location.reload();
            window.location.href = '/contents';
        })
        .fail(function(msg, textStatus){
            alert("登録に失敗しました。");
            console.log(msg.responseText);
        });
    }


    //file の情報を取得する
    function getFileData(){


        if($("#content").val()==''){

            $("#file_type").html('');
            $("#file_type_h").val('');

            $("#file_size").html('');
            $("#file_size_h").val('');

            $("#content_type").html('');
            $("#content_type_h").val('');
            $('#content_time').empty();
            $('#preview').empty();

        }else{
        var fileInput = $("#content");
        var files     = fileInput[0].files;
        var file_type = files[0].type;
        //時間を取得
        var element = document.createElement("video");
        element.src = URL.createObjectURL(files[0]);
        element.ondurationchange = function() {
            $('#content_time').html(this.duration + "秒");
            $('#content_time_h').val(this.duration);
            URL.revokeObjectURL(this.src);
        }

        //ファイルの基本情報を取得
        var bytes  = parseInt(files[0].size);
        var kbytes = bytes / 1024;
        var mbytes = bytes / (1024 * 1024);
        var c_size = "";

        kbytes = Math.round(kbytes * 100) / 100;
        mbytes = Math.round(mbytes * 100) / 100;

        if (mbytes > 1){
            c_size = `${mbytes} MB`;
        } else {
            c_size = `${kbytes} KB`;
        }

        var content_type = "";
        if (file_type.indexOf('image') !== -1){

            content_type = "静止画";

            //ファイルオブジェクトを取得する
            var file = files[0];
            var reader = new FileReader();

            $("#preview").html('<img id="img1" style="width:300px;height:300px;" />');

            //アップロードした画像を設定する
            reader.onload = (function(file){

                return function(e){
                    $("#img1").attr("src", e.target.result);
                    $("#img1").attr("title", file.name);
                };
            })(file);
            reader.readAsDataURL(file);

        } else if (file_type.indexOf('video') !== -1){
            content_type = "動画";

            //動画プレビュー表示
            $('#preview').html('<video controls style="width:300px;height:200px;"></video>');

            var video = document.querySelector('video');
            video.src = (URL).createObjectURL(files[0]);
        } else {
            content_type = "不明";
        }

        $("#file_type").html(files[0].type);
        $("#file_type_h").val(files[0].type);

        $("#file_size").html(c_size);
        $("#file_size_h").val(bytes);

        $("#content_type").html(content_type);
        $("#content_type_h").val(file_type);
        }
    }

    function previewFile(file) {

        // プレビュー画像を追加する要素
        const preview = $('#preview');

        // FileReaderオブジェクトを作成
        const reader = new FileReader();

        // URLとして読み込まれたときに実行する処理
        reader.onload = function (e) {
            const imageUrl = e.target.result;// URLはevent.target.resultで呼び出せる
            const img = document.createElement("img");// img要素を作成
            img.src = imageUrl;// URLをimg要素にセット
            preview.appendChild(img);//#previewの中に追加
        }

        // ファイルをURLとして読み込む
        reader.readAsDataURL(file);
    }



    </script>
@endsection
