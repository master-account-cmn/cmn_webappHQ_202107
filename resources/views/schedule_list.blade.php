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

    <div id="contents" style="width:80%;height:100%;margin:auto;">

        <table id="schedule_list" class="">

            <thead>
                <tr>
                    <th>No.</th>
                    <th>スケジュール名</th>
                    <th>表示開始日時</th>
                    <th>表示終了日時</th>
                    <th>操作</th>
                </tr>
            </thead>

            <tbody id="schedule_list_body">
                @foreach ($schedule as $scd)
                <tr id="tr_sid{{$scd->id}}">
                    <td class="no"></td>
                    <td><a href='javascript:void(0);' onclick='sche_display("{{$scd->id}}");'>{{$scd->schedule_name}}</a></td>
                    <input type='hidden' id='title{{$scd->id}}' value='{{$scd->schedule_name}}' />
                    <td>{{$scd->s_start}}</td>
                    <td>{{$scd->s_end}}</td>
                    <td>
                        <button class="btn btn-primary" onclick="sche_modify({{$scd->id}})" >編集</button>
                        <button class="btn btn-danger" onclick="confirm_del('s','tr_sid{{$scd->id}}','{{$scd->id}}')">削除</button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div style="width:100px;height:50px;margin:auto;">
            <input type="button" value="追加" onclick="sche_add();" />&nbsp;
            {{-- <input type="button" value="更新" onclick="" /> --}}
        </div>

        <table id="contents_list" class="tablesorter">

            <caption id="c_caption"></caption>

            <thead>
                <tr>
                    <th>表示順</th>
                    <th>コンテンツID</th>
                    <th>表示名</th>
                    <th>ファイル名</th>
                    <th>コンテンツタイプ</th>
                    <th>表示時間</th>
                </tr>
            </thead>

            <tbody id="contents_list_body"></tbody>

            <tfoot>
                <td colspan="5" style="text-align:right;">合計</td>
                <td id="total"></td>
            </tfoot>

        </table>

        <div>
            <input type="hidden" id="c_json" value="" /><!-- contents json  -->
            <input type="hidden" id="s_del"  value="" /><!-- schedule delete id -->
            <input type="hidden" id="c_del"  value="" /><!-- contents delete id -->
            <input type="hidden" id="sr_num" value="" /><!-- schedule next row -->
            <input type="hidden" id="cr_num" value="" /><!-- contents next row -->
        </div>

    </div>
    <script>
    // table num
    $(function(){
        $("td.no").each(function (i) {
        i = i+1;
        $(this).text(i);
        });
    });

    function sche_modify(SID){
        window.location.href = `/modify/schedule?id=${SID}`;
    }

    function sche_add(){
        window.location.href = "/create/schedule";
    }

    function sche_display(SID){

        $.ajax({
            type     : "POST",
            url      : "/schedule/display",
            data     : {"id" : SID},
            dataType : "json",
            headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        })
        .done(function(data){

            setContents(data);
            $("#c_caption").html($("#title" + SID).val());
        })
        .fail(function(XMLHttpRequest, status, e){
            alert(e);
        });
    }

    function confirm_del(TYPE, ROWID, ID){

        var json = '{';

        if (confirm("削除してもよろしいですか")){

            $("#" + ROWID).remove();
            $('#contents_list_body').empty();
            $('#c_caption').text('');
            $('#total').text('');
            $(function(){
                $("td.no").each(function (i) {
                i = i+1;
                $(this).text(i);
                });
            });

            if (ID){

                var del_json = $("#" + TYPE + "_del").val();

                if (del_json){
                    var json_obj = JSON.parse(del_json);
                    json_obj[ROWID] = ID;
                    $("#" + TYPE + "_del").val(JSON.stringify(json_obj));
                } else {
                    del_json = `{"${ROWID}":"${ID}"}`;
                    $("#" + TYPE + "_del").val(del_json);
                }
                schedule_del(ID);
            }

        }else{
            return false;
        }
    }
    function schedule_del(SID){

        $.ajax({
            type: "POST",
            url: "/delete/schedule",
            data: {"id" : SID},
            dataType : "json",
            headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        }).done(function(data){
            if(data=='success'){
                alert("削除しました。")
            }else{
                console.log(data);
                alert("削除に失敗しました。")
            }

        }).fail(function(XMLHttpRequest, status, e){
            alert(e);
        });
    }

    $(function() {
        jQuery.noConflict();
        $("#contents_list").tablesorter();

    });


    function addSchedule(){

        var rows = $('#schedule_list_body').prop('rows').length;//スケジュールの現在の行数
        var rnum = Number($("#sr_num").val());
        var rid  = `tr_sid${rnum}`;

        var tags  = `<tr id="${rid}">`;
            tags += `<td class="num">${(rows + 1)}</td>`;
            tags += '<td class=""><input type="text" id="name${rnum}" value="" /></td>';
            tags += `<td><input type="text" id="start${rnum}" class="cal" value="" /></td>`;
            tags += `<td><input type="text" id="end${rnum}" class="cal" value="" /></td>`;
            tags += '<td>';
            tags += '<input type="button" value="編集" onclick="" />&nbsp;&nbsp;';
            tags += `<input type="button" value="削除" onclick="confirm_del('s', '${rid}', '');" />`;
            tags += '</td>';
            tags += '</tr>';

            $("#schedule_list_body").append(tags);
            $("#sr_num").val(rnum+1);
    }


    function addContent(){

        var rows = $('#contents_list_body').prop('rows').length;

        var tags  = '<tr id="">';
            tags += `<td class="num">${(rows + 1)}</td>`;
            tags += '<td class="cid"></td>';//cid
            tags += '<td></td>';//name
            tags += '<td></td>';//file_name
            tags += '<td></td>';//content_type
            tags += '<td></td>';//play_time
            tags += '<td>';
            tags += '<input type="button" value="削除" onclick="confirm_del($(this).parent());" />';
            tags += '</td>';
            tags += '</tr>';

            $("#contents_list_body").append(tags);
    }



    function updatePlayList(){

        var json = '{';

        $('#contents_list_body > tr').each(function(i, e){

            var row_num = $(this).children(".num").html();
            var cid     = $(this).children(".cid").html();

            json += `"${row_num}":"${cid}",`;
        });

        json = json.substring(0, json.length -1);
        json += '}';
    }


    function ajax_test(){

        $.ajax({
            type: "POST",
            url: "schedule.php",
            data: {"id" : "777"},
            dataType : "json"
        }).done(function(data){

            setSchedule(data);

        }).fail(function(XMLHttpRequest, status, e){
            alert(e);
        });
    }

    function setSchedule(DATA){

        var tags = '';

        for (let step = 0; step < DATA.length; step++) {

            var data = DATA[step];

            tags += `<tr id="tr_sid${data.id}">`;
            tags += `<td class="num">${(step + 1)}</td>`;
            tags += `<td class="">${data.name}</td>`;//name
            tags += '<td>';//start
            tags += `<input type="text" id="start${data.id}" value="${data.s_start}" onclick="" />`;//start
            tags += '</td>';//start
            tags += '<td>';//end
            tags += `<input type="text" id="end${data.id}" value="${data.s_end}" onclick="" />`;//end
            tags += '</td>';//end
            tags += '<td>';
            tags += '<input type="button" value="編集" onclick="" />';
            tags += `<input type="button" value="削除" onclick="confirm_del('tr_sid${data.id}');" />`;
            tags += '</td>';
            tags += '</tr>';

        }

        $("#schedule_list_body").html(tags);
    }


    function setContents(DATA){

        var json = '{';
        var c_type   = {"1":"静止画", "10":"動画"};

        var tags  = '';
        var total = 0;

        for (let step = 0; step < DATA.length; step++) {

            var data = DATA[step];

            json += `"${(step + 1)}":"${data.cid}",`;

            var dtime = Math.round(parseFloat(data.display_time)*10)/10;

            tags += `<tr id="tr_sid${data.id}">`;
            tags += `<td class="num">${(step + 1)}</td>`;
            tags += `<td class="cid">${data.cid}</td>`;//cid
            tags += `<td class="">${data.display_name}</td>`;//title
            tags += `<td class="">${data.file_name}</td>`;//file_name
            tags += '<td>' + c_type[data.content_type] + '</td>';//content_type
            tags += `<td class="">${dtime}秒</td>`;//display_time
            tags += '</tr>';

            total += parseFloat(data.display_time);
        }

        json = json.substring(0, json.length -1);
        json += '}';

        total = Math.round(parseFloat(total)*10)/10 + "秒";

        $("#contents_list_body").html(tags);
        $("#total").html(total);
    }


    </script>
@endsection
