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

    <!-- 3つめ
    <li itemprop="itemListElement" itemscope
        itemtype="https://schema.org/ListItem">
      <a itemprop="item" href="子カテゴリーのURL">
          <span itemprop="name">子カテゴリー名</span>
      </a>
      <meta itemprop="position" content="3" />
    </li>-->
    </ol>

    <table id="contents_list" class="tablesorter">

      <thead>
            <tr>
                <th>No.</th>
                <th>コンテンツID</th>
                <th>表示名</th>
                <th>ファイル名</th>
                <th>コンテンツタイプ</th>
                <th>表示時間</th>
                <th>操作</th>
            </tr>
        </thead>

        <tbody id="contents_list_body">
            @foreach ($contents as $content)
            <tr id="tr-{{$content->id}}">
                <td class="no">&nbsp;</td>
                <td>{{$content->cid}}</td>
                <td>{{$content->display_name}}</td>
                <td>{{$content->file_name}}</td>
                @if ($content->content_type==1)
                    <td>静止画</td>
                @else
                    <td>動画</td>
                @endif
                <td>{{round($content->display_time,1)}}秒</td>
                <td>
                    <button class="btn btn-primary" onclick="content_modify({{$content->id}})" >編集</button>
                    <button class="btn btn-danger" onclick="confirm_del({{$content->id}})">削除</button>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div style="width:100px;height:50px;margin:auto;">
        <input type="button" value="追加" onclick="window.location.href = '/create/content'" />
    </div>


    <script>
    // table num
    $(function(){
        $("td.no").each(function (i) {
        i = i+1;
        $(this).text(i);
        });
    });

    function confirm_del(ID){
        if (confirm("削除してもよろしいですか")){


            if (ID){
                //DB を更新する
                deleteContents(ID);
            }
            //対象行を削除
            $(this).parents('tr').remove();

            //行番号の振り直し
            $(function(){
                $("td.no").each(function (i) {
                i = i+1;
                $(this).text(i);
                });
            });
        }
    }


    function deleteContents(ID){

        //削除データ
        var json = {
            "id" : ID
        };

        $.ajax({
            type     : "POST",
            url      : "../delete/content",
            data     : json,
            dataType : "json",
            headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        }).done(function(data){
            if(data=='success'){
                alert("削除しました");
                location.reload();
            }else{
                alert('失敗しました。');
            }
        }).fail(function(XMLHttpRequest, status, e){
            alert(e);
        });
    }

    function content_modify(UID){
        window.location.href = `/modify/content?id=${UID}`;
    }


$(function() {
    jQuery.noConflict();
	$("#contents_list").tablesorter();

});
    </script>
@endsection
