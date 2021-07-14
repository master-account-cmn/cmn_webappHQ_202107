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
                    <th>初回登録</th>
                    <th>前回更新</th>
                </tr>
            </thead>

            <tbody id="schedule_list_body">
                <tr>
                    <td id='sid'>{{$schedule->id}}</td>
                    <td><input type="text" id="name" value="{{$schedule->schedule_name}}"></td>
                    <td><input type="text" id="s_start" class="cal" value="{{date('Y-m-d H:i', strtotime($schedule->s_start))}}" readonly="readonly"></td>
                    <td><input type="text" id="s_end" class="cal" value="{{date('Y-m-d H:i', strtotime($schedule->s_end))}}" readonly="readonly"></td>
                    <td>{{$schedule->created_at}}</td>
                    <td>{{$schedule->updated_at}}</td>
                </tr>
            </tbody>
        </table>

        <div style="width:100px;height:50px;margin:auto;">
            <input type="button" value="更新" onclick="updateSchedule();" />
        </div>

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
            @php
                $total;
            @endphp
            <tbody id="contents_list_body">
                @foreach ($contents as $content)
                <tr id="tr_cid{{$content->cid}}">
                    <td class="no"></td>
                    <td class="cid">{{$content->cid}}</td>
                    <td>{{$content->display_name}}</td>
                    <td>{{$content->file_name}}</td>
                    @if ($content->content_type==1)
                        <td>静止画</td>
                    @else
                        <td>動画</td>
                    @endif
                    <td class="dis-time" data-time="{{$content->display_time}}">{{round($content->display_time,1)}}秒</td>
                    <td>
                        <button onclick="confirm_del('tr_cid{{$content->cid}}','{{$content->cid}}')">削除</button>
                        <input type="hidden" class="dtime" id="dtime-{{$content->id}}" value="{{$content->display}}">
                    </td>
                </tr>
                @endforeach
            </tbody>

            <tfoot>
                <td colspan="5" style="text-align:right;">合計</td>
                <td id="total"></td>
                <td></td>
            </tfoot>

        </table>


        <div style="width:100px;height:50px;margin:auto;">
            <input type="button" value="追加" onclick="addContent();" />&nbsp;
            <input type="button" value="更新" onclick="updatePlayList();" />

        </div>

        <div>
            <!-- contents json  -->
            <!--<input type="hidden" id="c_json" value="" />-->
            <!-- contents delete id -->
            <!--<input type="hidden" id="c_del"  value="" />-->
            <!-- contents next row -->
            <input type="hidden" id="cr_num" value="" />
        </div>

    </div>

<script>

    $(function(){
        table_no();
        time_display();

    });
    function table_no(){
        $("td.no").each(function (i) {
            i = i+1;
            $(this).text(i);
            $('#cr_num').val(i)
        });
    }
    function time_display(){
        var total = 0;
        $("td.dis-time").each(function () {
            var num = parseFloat($(this).data('time'));
            if(num!==NaN    ){
                total += num ;
            }
            $('#total').text(total.toFixed(1)+'秒');
        });
    }
    function sche_modify(SID){
        $.ajax({
            type: "POST",
            url: "/schedule/display",
            data: {"id" : SID},
            dataType : "json",
            headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        })
        .done(function(data){
            setContents(data);
        })
        .fail(function(XMLHttpRequest, status, e){
            alert(e);
        });
    }

    function confirm_del(ROWID, ID){

        if (confirm("削除してもよろしいですか")){

            //対象行を削除
            $("#" + ROWID).remove();

            if (ID){
                //DB を更新する
                updateContentsLineup();
            }

            var rows = $('#schedule_list_body').prop('rows').length;//スケジュールの削除後の行数
            console.log(rows);
            //行番号の振り直し
            table_no();
        }
    }

    $(function() {
        jQuery.noConflict();
        $("#contents_list").tablesorter();

        $('#contents_list_body').sortable({

            stop: function( event, ui ) {

                $('#contents_list_body > tr').each(function(i, e){
                    var cid = $(this).children(".cid").html();
                });
                // tableの行番号
                table_no();
            }
        });

        $(".cal").datetimepicker({
            dateFormat: "yy-mm-dd",
        });

    });

    //並び順の json を作成する
    function makeLineupJson(){

        var json = '{';

        $('#contents_list_body > tr').each(function(i, e){

            var new_row_num = i + 1;
            var cid         = $(this).children(".cid").html();



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

            var next_num = parseInt($("#cr_num").val())+1;
            var rows     = $('#contents_list_body').prop('rows').length;
            var selector = `<select id="" onchange="setContentData($(this).parent().attr('numid'),$(this).val());">`;
            selector    += `<option value="">選択してください</option>`;

            for (const i in data) {
                selector += `<option value="${data[i].cid}">${data[i].name}</option>`;
            }

            selector += '</select>';

            var tags  = `<tr id="newtr-${next_num}">`;
                tags += `<td class="no">${(rows + 1)}</td>`;
                tags += `<td class="cid" id="newcid-${next_num}"></td>`;//cid
                tags += `<td numid="${next_num}">${selector}</td>`;//name
                tags += `<td id="newfname-${next_num}"></td>`;//file_name
                tags += `<td id="newctype-${next_num}"></td>`;//content_type
                tags += `<td id="newtime-${next_num}"></td>`;//play_time
                tags += '<td class="op">';
                tags += `<input type="button" value="削除" onclick="confirm_del('newtr-${next_num}','');" />`;
                tags += `<input type="hidden" class="dtime" id="dtime-${next_num}" value=""  />`;
                tags += '</td>';
                tags += '</tr>';

            $("#contents_list_body").append(tags);
            $("#cr_num").val(next_num);


        }).fail(function(XMLHttpRequest, status, e){
            alert(e);
        });

    }

    // //表示するコンテンツを取得する
    // function getContents(){

    //     $.ajax({
    //         type     : "POST",
    //         url      : "getcontents.php",
    //         data     : {"dammy" : "777"},
    //         dataType : "json"
    //     })
    // }

    //コンテンツのデータをセットする
    function setContentData(NUM, CID){
        console.log(NUM);
        //最初のオプションが選択された場合
        if (!CID){

            $("#newcid-"   + NUM).html("");
            $("#newfname-" + NUM).html("");
            $("#newctype-" + NUM).html("");
            $("#newtime-"  + NUM).html("");
            $("#newtime-"  + NUM).data('time','0');
            $("#dtime-"    + NUM).val(0);

            time_display();
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
            $("#newtime-"  + NUM).addClass('dis-time')
            $("#newtime-"  + NUM).data('time',data[0].display_time)
            $("#dtime-"    + NUM).val(data[0].display_time);

            time_display();

        }).fail(function(XMLHttpRequest, status, e){
            alert(e);
        });
    }

    //表示合計時間
    // function totalDisplayTime(){
    //     $("#total").html('');
    //     var total = 0;

    //     $('#contents_list_body > tr').each(function(i, e){

    //         var dtime = parseFloat($(this).children(".op").children(".dtime").val());

    //         total += Math.round(dtime * 10) / 10;
    //     });

    //     total += "秒";

    //     $("#total").html(total);
    // }


    function updatePlayList(){

        var judge = false;

        $('#contents_list_body > tr').each(function(i, e){

            var cid = $(this).children(".cid").html();

            if (!cid){
                judge = true;
            }
        });

        if (judge){
            alert("入力されていない行があります");
        } else {
            updateContentsLineup();
        }
    }


    // function ajax_test(){

    //     $.ajax({
    //         type: "POST",
    //         url: "schedule.php",
    //         data: {"id" : "777"},
    //         dataType : "json"
    //     }).done(function(data){

    // console.log(data.length);

    //         setSchedule(data);
    // //const obj = JSON.parse(data);

    //     }).fail(function(XMLHttpRequest, status, e){
    //         alert(e);
    //     });
    // }




    function setContents(DATA){

        var json = '{';
        var c_type   = {"1":"静止画", "10":"動画"};

        var tags = '';

        for (let step = 0; step < DATA.length; step++) {

            var data = DATA[step];

            json += `"${(step + 1)}":"${data.cid}",`;

            tags += `<tr id="tr_sid${data.id}">`;
            tags += `<td class="no">${(step + 1)}</td>`;
            tags += `<td class="cid">${data.cid}</td>`;//cid
            tags += `<td class="">${data.display_name}</td>`;//title
            tags += `<td class="">${data.file_name}</td>`;//file_name
            tags += '<td>' + c_type[data.content_type] + '</td>';//content_type
            tags += `<td class="">${data.display_time}秒</td>`;//display_time
            tags += '<td>';
            tags += `<input type="button" value="削除" onclick="confirm_del('tr_cid${data.id}');" />`;
            tags += '</td>';
            tags += '</tr>';
        }

        json  = json.substring(0, json.length -1);
        json += '}';

        $("#contents_list_body").html(tags);
    }


    function updateSchedule(){

        var start = new Date($(s_start).val());
        var end = new Date($(s_end).val());
        var diff = end.getTime() - start.getTime();
        var diff_hour = Math.ceil(diff / (1000 * 60 * 60) );
        if(diff_hour <= 0){
            alert("不正な入力値です。")
            return false;
        }
        if($("#name").val().length > 255){
            alert("スケジュール名は255文字以下でなければなりません");
            return false;
        }


        //UPDATE のデータを取得する
        var update_json = {
            "id"      : $("#sid").html(),
            "name"    : $("#name").val(),
            "s_start" : $("#s_start").val(),
            "s_end"   : $("#s_end").val()
        };

        $.ajax({
            type     : "POST",
            url      : "/update/schedule",
            data     : update_json,
            dataType : "json",
            headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        }).done(function(data){
            if(data==1){
                alert("更新しました");
            }else{
                alert("失敗しました。")
                console.log(data);
            }
        }).fail(function(XMLHttpRequest, status, e){
            alert(e);
        });
    }

    //コンテンツの再生順を変更する
    function updateContentsLineup(){

        var list_json = makeLineupJson();//並び順の json 文字列を取得

        //並び順のデータ
        var lineup_json = {
            "id"      : $("#sid").html(),
            "s_list"    : list_json
        };

        $.ajax({
            type     : "POST",
            url      : "/update/c_schedule",
            data     : lineup_json,
            dataType : "json",
            headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        }).done(function(data){
            if(data==1){
                alert("更新しました");
                location.reload();
            }else{
                alert("失敗しました。")
                console.log(data);}
        }).fail(function(XMLHttpRequest, status, e){
            alert(e);
        });

    }


    function test(){

        alert($("#s_del").val());

    }

    </script>

@endsection
