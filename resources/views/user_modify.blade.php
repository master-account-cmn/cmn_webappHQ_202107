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

<div>

</div>

<form id="user_form">

    <table id="user_list" class="tablesorter">

        <thead>
            <tr>
                <th>項目</th>
                <th>更新</th>
                <th>内容</th>
            </tr>
        </thead>

        <tbody>

            <tr>
                <th>ID</th>
                <td>
                    <input type="hidden" name="id" id="id" value="{{$user->id}}" >
                </td>
                <td>{{$user->id}}</td>
            </tr>

            <tr>
                <th>アカウント</th>
                <td></td>
                <td>{{$user->name}}</td>
            </tr>

            <tr>
                <th>表示名</th>
                <td>
                    <input type="checkbox" name="c_display_name" id="c_display_name" value="1" />
                </td>
                <td>
                    <input type="text" name="display_name" id="display_name" value="{{$user->display_name}}" />
                </td>
            </tr>

            <tr>
                <th>旧パスワード</th>
                <td>
                    <input type="checkbox" name="c_new_password" id="c_password" value="1" style="ime-mode: disabled"/>
                </td>
                <td>
                    <input type="password" name="password" id="password" value="" />
                </td>
            </tr>

            <tr>
                <th>新パスワード</th>
                <td></td>
                <td>
                    <input type="password" name="new_password" id="new_password" value="" style="ime-mode: disabled"/>
                </td>
            </tr>

            <tr>
                <th>新パスワード（確認）</th>
                <td></td>
                <td>
                    <input type="password" name="renew_password" id="renew_password" value="" style="ime-mode: disabled"/>
                </td>
            </tr>

            <tr>
                <th>メールアドレス</th>
                <td>
                    <input type="checkbox" name="c_email" id="c_email" value="1" />
                </td>
                <td>
                    <input type="text" name="email" id="email" value="{{$user->email}}" />
                </td>
            </tr>

            <tr>
                <th>メールアドレス（確認）</th>
                <td></td>
                <td>
                    <input type="text" name="new_email" id="new_email" value="" />
                </td>
            </tr>

            @if (Auth::user()->authority==10)
            <tr>
                <th>権限</th>
                <td>
                    <input type="checkbox" name="c_authority" id="c_authority" value="1" />
                </td>
                <td>
                    @if ($user->authority == '10')
                    <label><input type="radio" name="authority" id="authority10" value="10"checked/>管理者</label>
                    <label><input type="radio" name="authority" id="authority1" value="1"/>一般ユーザー</label>
                    @else
                    <label><input type="radio" name="authority" id="authority10" value="10"/>管理者</label>
                    <label><input type="radio" name="authority" id="authority1" value="1"checked/>一般ユーザー</label>
                    @endif

                </td>
            </tr>

            <tr>
                <th>有効性</th>
                <td>
                    <input type="checkbox" name="c_validity" id="c_validity" value="1" />
                </td>
                <td>
                    @if ($user->validity == '0')
                    <label><input type="radio" name="validity" id="validity0" value="0"checked/>利用可能</label>
                    <label><input type="radio" name="validity" id="validity1" value="1"/>利用不許可</label>
                    @else
                    <label><input type="radio" name="validity" id="validity0" value="0"/>利用可能</label>
                    <label><input type="radio" name="validity" id="validity1" value="1"checked/>利用不許可</label>
                    @endif
                </td>
            </tr>
            @endif

        </tbody>
    </table>
</form>

<div style="width:100px;height:50px;margin:auto;">
    <input type="button" value="更新" onclick="updateCheck();" />
</div>
<script>
function updateCheck(){

    //チェックが付いているものを確認
    var formData = $("#user_form").serializeArray();

    var data   = {};
    var update = [];
    var result = true;

    $.each(formData, function(i, element) {

        if (element.name.indexOf("c_") != -1){
            update.push(element.name.replace('c_', ''));
        }
        data[element.name] = element.value;
    });

    var update_json = {
        "id":data["id"]
    }

    //入力チェック
    jQuery.each(update, function(i, val){
        //console.log(i + ":" + val);

        var len = data[val].trim().length;

        //表示名
        if (val == "display_name"){

            //var len = data[val].trim().length;

            if (len == 0 || len > 40){
                alert("表示名は１～40文字で入力してください");
                result = false;
                return false;
            }
            if ($('#display_name').val().match( /[^ -/:-@\[-~]+/ )){
                alert("表示名に無効な文字が入力されています");
                result = false;
            }
        }

        //新パスワード
        else if (val == "new_password"){

            //var len = data[val].trim().length;

            if (len < 7){
                alert("パスワードは7文字以上で入力してください");
                result = false;
                return false;
            }
            if ($('#new_password').val().match( /[^A-Za-z0-9s.-]+/ )){
                alert("パスワードは半角英文字で入力してください");
                result = false;
                return false;
            }


            if (data[val] != data["renew_password"]){
                alert("パスワードが一致しません");
                result = false;
                return false;
            }

            update_json["password"] = data["password"];
        }

        //メールアドレス
        else if (val == "email"){

            //var len = data[val].trim().length;
            if (!$('#email').val().match(/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/)){
                alert("無効なメールアドレスです");
                result = false;
                return false;
            }

            if (len == 0){
                alert("メールアドレスが未入力です");
                result = false;
                return false;
            }

            if (data[val] != data["new_email"]){
                alert("メールアドレスが一致しません");
                result = false;
                return false;
            }
        }
        update_json[val] = data[val];

    });
    if(result !== false){
        ajax_request(update_json);
    }

}
function ajax_request(update_json){
    if(Object.keys(update_json).length <= 1){
        alert('更新する内容がありません。')
        return false;
    }
    $.ajax({
        type     : "POST",
        url      : "../update/user",
        data     : update_json,
        dataType : "json",
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    }).done(function(data){
        if(data ==true){
            alert("更新しました。");
            window.location.href = '../users';
        }else{
            alert("更新に失敗しました。");
        }
    }).fail(function(XMLHttpRequest, status, e){
        alert(e);
    });
}
</script>
@endsection
