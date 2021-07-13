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
        <a itemprop="item" href="/modify/content?id={{$content->id}}">
            <span itemprop="name">コンテンツ情報編集</span>
        </a>
        <meta itemprop="position" content="3" />
        </li>
    </ol>

    <a href="../contents">戻る</a>
    <table>

        <tr>
            <th>ID</th>
            <td>{{$content->id}}</td>
        </tr>

        <tr>
            <th>コンテンツID</th>
            <td>{{$content->cid}}</td>
        </tr>

        <tr>
            <th>題名</th>
            <td>
                <input type="text" class="len400" id="name" value="{{$content->name}}" />
            </td>
        </tr>

        <tr>
            <th>表示名</th>
            <td>
                <input type="text" class="len400" id="display_name" value="{{$content->display_name}}" />
            </td>
        </tr>

        <tr>
            <th>ファイル名</th>
            <td>{{$content->file_name}}</td>
        </tr>

        <tr>
            <th>コンテンツタイプ</th>
            @if ($content->content_type == 1)
                <td>静止画</td>
            @else
                <td>動画</td>
            @endif
        </tr>

        <tr>
            <th>表示時間</th>
            <td>{{round($content->display_time,1)}}秒</td>
        </tr>

        <tr>
            <th>プレビュー</th>
            @if ($content->content_type == 1)
                <td><img src="{{ Storage::url($content->file_name) }}" alt="" style="width:300px;height:200px;"></td>
            @else
                <td>
                    <video controls style="width:300px;height:200px">
                        <source src="{{ Storage::url($content->file_name) }}">
                    </video>
                </td>
            @endif
        </tr>

    </table>

      <div style="width:100px;height:50px;margin:auto;">
          <input type="button" value="更新" onclick="updateContent('{{$content->id}}');" />
      </div>
    <script>

        function updateContent(ID){

            //UPDATE データを取得
            var json = {
                "id"    : ID,
                "name"  : $("#name").val(),
                "display_name" : $("#display_name").val()
            }

            $.ajax({
                type     : "POST",
                url      : "/update/content",
                data     : json,
                dataType : "json",
                headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            }).done(function(data){
                console.log(data);
                if(data==1){
                    alert("更新しました");
                    window.location.href = '../contents';
                }else{
                    alert('失敗しました。')
                }
            }).fail(function(XMLHttpRequest, status, e){
                alert(e);
            });
        }

    </script>
@endsection
