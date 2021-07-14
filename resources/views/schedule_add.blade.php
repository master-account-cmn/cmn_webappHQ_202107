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
        <a itemprop="item" href="/schedule">
            <span itemprop="name">スケジュール管理</span>
        </a>
        <meta itemprop="position" content="2" />
        </li>

        <!-- 3つめ
        <li itemprop="itemListElement" itemscope
            itemtype="https://schema.org/ListItem">
        <a itemprop="item" href="子カテゴリーのURL">
            <span itemprop="name">子カテゴリー名</span>
        </a>
        <meta itemprop="position" content="3" />
        </li>-->
    </ol>

    <a href="/schedule">戻る</a>

    <div id="contents" style="width:80%;height:100%;margin:auto;">

        <table id="schedule_list" class="">

            <thead>
                <tr>
                    <th>ID</th>
                    <th>スケジュール名</th>
                    <th>表示開始日時</th>
                    <th>表示終了日時</th>
                </tr>
            </thead>

            <tbody id="schedule_list_body">
                <tr id=''>
                    <td id='sid'>自動発番</td>
                    <td class=''>
                        <input type='text' id='name' value='' />
                    </td>
                    <td>
                        <input type='text' id='s_start' class='cal' readonly="readonly" value='' />
                    </td>
                    <td>
                        <input type='text' id='s_end' class='cal' readonly="readonly" value='' />
                    </td>
                </tr>
            </tbody>
        </table>



        <table id="contents_list" class="tablesorter">

            <thead>
                <tr>
                    <th>表示順</th>
                    <th>コンテンツID</th>
                    <th>表示名</th>
                    <th>ファイル名</th>
                    <th>コンテンツタイプ</th>
                    <th>表示時間</th>
                    <th>操作</th>
                </tr>
            </thead>

            <tbody id="contents_list_body"></tbody>

            <tfoot>
                <td colspan="5" style="text-align:right;">合計</td>
                <td id="total"></td>
                <td id=""></td>
            </tfoot>

        </table>


        <div style="width:100px;height:50px;margin:auto;">
            <input type="button" value="追加" onclick="addContent();" />&nbsp;
        </div>

        <div style="width:100px;height:50px;margin:auto;">
            <input type="button" value="登録" onclick="insertSchedule();" />
        </div>

        <div>
            <!-- contents json  -->
            <!--<input type="hidden" id="c_json" value="" />-->
            <!-- contents delete id -->
            <!--<input type="hidden" id="c_del"  value="" />-->
            <!-- contents next row -->
            <input type="hidden" id="cr_num" value="1" />
        </div>

    </div>
<script>
    function confirm_del(ROWID, ID){

        if (confirm("削除してもよろしいですか")){

            //対象行を削除
            $("#" + ROWID).remove();

            // if (ID){
            //     //DB を更新する
            //     updateContentsLineup();
            // }

            var rows = $('#schedule_list_body').prop('rows').length;//スケジュールの削除後の行数

            //行番号の振り直し
            $('#schedule_list_body > tr').each(function(i, e){

                var new_row_num = i + 1;

                $(this).children(".num").html(new_row_num);
            });
            makeLineupJson();
            totalDisplayTime();
        }
    }

    $(function() {
    /*
        $("#contents_list").tablesorter();
    */
        // ドラッグでテーブルの並びかえ
        jQuery.noConflict();
        $('#contents_list_body').sortable({

            stop: function( event, ui ) {

                $('#contents_list_body > tr').each(function(i, e){

                    var new_row_num = i + 1;
                    var cid         = $(this).children(".cid").html();

                    $(this).children(".num").html(new_row_num);
                });
            }
        });

        $(".cal").datetimepicker({
            dateFormat: "yy-mm-dd",
            minDate: 0,
        });
    });

    //並び順の json を作成する
    function makeLineupJson(){

        var json = '{';

        $('#contents_list_body > tr').each(function(i, e){

            var new_row_num = i + 1;
            var cid         = $(this).children(".cid").html();

            $(this).children(".num").html(new_row_num);

            json += `"${new_row_num}":"${cid}",`;
        });

        json  = json.substring(0, json.length -1);
        json += '}';

        return json;
    }



    //表示するコンテンツを追加する
    function addContent(){

        //表示可能なコンテンツを取得する
        $.ajax({
            type     : "POST",
            url      : "/get/content",
            data     : {"repetition" : $("#repetition").prop('checked')},
            dataType : "json",
            headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        })
        .done(function(data){

            var next_num = parseInt($("#cr_num").val());
            var rows     = $('#contents_list_body').prop('rows').length;
            var selector = `<select id="" onchange="setContentData($(this).parent().attr('numid'),$(this).val());">`;
            selector    += `<option value="">選択してください</option>`;

            for (const i in data) {
                selector += `<option value="${data[i].cid}">${data[i].name}</option>`;
            }

            selector += '</select>';

            var tags  = `<tr id="newtr-${next_num}">`;
                tags += `<td class="num">${(rows + 1)}</td>`;
                tags += `<td class="cid" id="newcid-${next_num}"></td>`;//cid
                tags += `<td numid="${next_num}">${selector}</td>`;//name
                tags += `<td id="newfname-${next_num}"></td>`;//file_name
                tags += `<td id="newctype-${next_num}"></td>`;//content_type
                tags += `<td id="newtime-${next_num}"></td>`;//display_time
                tags += '<td class="op">';
                tags += `<input type="button" value="削除" onclick="confirm_del('newtr-${next_num}','');" />`;
                tags += `<input type="hidden" class="dtime" id="dtime-${next_num}" value=""  />`;
                tags += '</td>';
                tags += '</tr>';

            $("#contents_list_body").append(tags);
            $("#cr_num").val(next_num + 1);


        }).fail(function(XMLHttpRequest, status, e){
            alert(e);
        });

    }


    //コンテンツのデータをセットする
    function setContentData(NUM, CID){

        //最初のオプションが選択された場合
        if (!CID){

            $("#newcid-"   + NUM).html("");
            $("#newfname-" + NUM).html("");
            $("#newctype-" + NUM).html("");
            $("#newtime-"  + NUM).html("");
            $("#dtime-"    + NUM).val(0);

            totalDisplayTime();

            return false;
        }

        //表示可能なコンテンツを取得する
        $.ajax({
            type     : "POST",
            url      : "/get/content",
            data     : {"cid" : CID},
            dataType : "json",
            headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        })
        .done(function(data){
            var c_type   = {"1":"静止画", "10":"動画"};
            var dtime = Math.round(parseFloat(data[0].display_time) * 10) / 10;

            $("#newcid-"   + NUM).html(data[0].cid);
            $("#newfname-" + NUM).html(data[0].file_name);
            $("#newctype-" + NUM).html(c_type[data[0].content_type]);
            $("#newtime-"  + NUM).html(dtime + "秒");
            $("#dtime-"    + NUM).val(data[0].display_time);

            totalDisplayTime();

        }).fail(function(XMLHttpRequest, status, e){
            alert(e);
        });
    }

    //表示合計時間
    function totalDisplayTime(){

        var total = 0;

        $('#contents_list_body > tr').each(function(i, e){

            var dtime = parseFloat($(this).children(".op").children(".dtime").val());
            total += Math.round(dtime * 10) / 10;
        });

        total += "秒";

        $("#total").html(total);
    }


    function insertSchedule(){
        var start = new Date($(s_start).val());
        var end = new Date($(s_end).val());
        var diff = end.getTime() - start.getTime();
        var diff_hour = Math.ceil(diff / (1000 * 60 * 60) );
        if(diff_hour <= 0){
            alert("日時が不正な入力値です。")
            return false;
        }

        //表示コンテンツを取得する
        var judge  = false;
        var tr_num = parseInt($('#contents_list_body > tr').length);

        //未入力チェック
        if (!$("#name").val() || !$("#s_start").val()){
            alert("入力されていない項目があります");
            return false;
        }
        if($("#name").val().length > 255){
            alert("スケジュール名は255文字以下でなければなりません");
            return false;
        }

        //日付
        isDateTime($("#s_start").val());

        var list_json = "";//並び順の json 文字列

        if (tr_num > 0){

            $('#contents_list_body > tr').each(function(i, e){

                var cid = $(this).children(".cid").html();

                if (!cid){
                    judge = true;
                }
            });

            if (judge){
                alert("入力されていない行があります");
                return false;
            } else {

                list_json = makeLineupJson();//並び順の json 文字列を取得
            }
        } else {
            list_json = "{}";
        }

        //INSERT のデータを取得する
        var insert_json = {
            "schedule_name": $("#name").val(),
            "s_start" : $("#s_start").val(),
            "s_end"   : $("#s_end").val(),
            "s_list"    : list_json
        };

        $.ajax({
            type     : "POST",
            url      : "/store/schedule",
            data     : insert_json,
            dataType : "json",
            headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        }).done(function(data){
            if(data=='success'){
                alert("登録しました");
                window.location.href = '/schedule';
            }else{
                alert("登録に失敗しました。")
                console.log(data);
            }
        }).fail(function(XMLHttpRequest, status, e){
            alert(e);
        });
    }

    // //コンテンツの再生順を変更する
    // function updateContentsLineup(){

    //     var list_json = makeLineupJson();//並び順の json 文字列を取得

    //     //並び順のデータ
    //     var lineup_json = {
    //         "id"      : $("#sid").html(),
    //         "list"    : list_json
    //     };

    //     $.ajax({
    //         type     : "POST",
    //         url      : "lineup_update.php",
    //         data     : lineup_json,
    //         dataType : "json"
    //     }).done(function(data){
    //         alert("更新しました");
    //         location.reload();
    //     }).fail(function(XMLHttpRequest, status, e){
    //         alert(e);
    //     });

    // }


    // function test(){

    //     alert($("#s_del").val());

    // }

    //日時のチェック用
    function isDateTime(strDate){

        // 空文字は無視
        if(strDate == ""){
            return true;
        }

        //文字列を分解
        var dateTime = strDate.split(" ");

        var y = dateTime[0].split("-")[0];
        var m = dateTime[0].split("-")[1] - 1;
        var d = dateTime[0].split("-")[2];

        var date = new Date(y,m,d);

        if(    date.getFullYear() != y
            || date.getMonth()    != m
            || date.getDate()     != d
        ){
            return false;
        }

        if (!dateTime[1] || !dateTime[1].split(":")[1]){
            return false;
        }

        var H = parseInt(dateTime[1].split(":")[0], 10);
        var i = parseInt(dateTime[1].split(":")[1], 10);

        if (0 <= H && H <= 23 && 0 <= i && i <= 59) {
            return true;
        } else {
            return false;
        }
    }

</script>
@endsection
