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
        <a itemprop="item" href="/users">
            <span itemprop="name">ユーザー管理</span>
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
@if (Auth::user()->authority == 10)
<div>
    <a href="/create/user">新規登録</a>
</div>
@endif
<table id="user_list" class="tablesorter">

    <thead>
        <tr>
            <th>No.</th>
            <th>ID</th>
            <th>アカウント</th>
            <th>表示名</th>
            <th>権限</th>
            <th>管理</th>
            <th>操作</th>
        </tr>
    </thead>

    <tbody>
        @foreach ($users as $user)
        <tr id="{{$user->id}}">
            <td class="no">&nbsp;</td>
            <td>{{$user->id}}</td>
            <td>{{$user->name}}</td>
            <td>{{$user->display_name}}</td>
            @if ($user->authority=='1')
            <td>一般ユーザー</td>
            @elseif ($user->authority=='10')
            <td>管理者</td>
            @endif
            @if ($user->validity == '0')
            <td>使用可能</td>
            @else
            <td>使用不可</td>
            @endif
            <td>
                <button class="btn btn-primary" onclick="user_modify({{$user->id}})" >編集</button>
                @if (Auth::user()->authority == 10)
                <button class="btn btn-danger" onclick="confirm_del({{$user->id}})">削除</button>
                @endif
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
<script type="text/javascript">
    // table num
    $(function(){
        $("td.no").each(function (i) {
        i = i+1;
        $(this).text(i);
        });
    });

    function confirm_del(UID){

    if (confirm("このユーザーを削除してもいいですか")){

    $.ajax({
        type     : "POST",
        url      : "../delete/user",
        data     : {"id":UID},
        dataType : "json",
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    }).done(function(data){
        console.log(data);
        alert("削除しました");
        window.location.href = 'schedule_list.php';
        location.reload();
    }).fail(function(XMLHttpRequest, status, e){
        alert(e);
    });
}
}

$(function() {
    jQuery.noConflict();
    $(".tablesorter").tablesorter();
});


function user_modify(UID){
window.location.href = `/modify/user?id=${UID}`;
}

</script>
@endsection
