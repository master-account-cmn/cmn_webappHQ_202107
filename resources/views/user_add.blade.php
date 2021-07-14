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

<!-- 3つめ -->
<li itemprop="itemListElement" itemscope
itemtype="https://schema.org/ListItem">
<a itemprop="item" href="/create/user">
    <span itemprop="name">ユーザー情報登録</span>
</a>
<meta itemprop="position" content="3" />
</li>
</ol>

<form id="user_form" method="post" >

    <table>

        <tr>
            <th>ユーザーID</th>
            <td>
                自動発番
            </td>
        </tr>

        <tr>
            <th>アカウント</th>
            <td>
                <input type="text" class="len400" name="name" id="name" value="" />
            </td>
        </tr>

        <tr>
            <th>パスワード</th>
            <td>
                <input type="password" class="len400" name="password" id="password" value="" />
            </td>
        </tr>

        <tr>
            <th>パスワード（確認）</th>
            <td>
                <input type="password" class="len400" name="c_password" id="c_password" value="" />
            </td>
        </tr>

        <tr>
            <th>表示名</th>
            <td>
                <input type="text" class="len400" name="display_name" id="display_name" value="" />
            </td>
        </tr>

        <tr>
            <th>メールアドレス</th>
            <td>
                <input type="text" class="len400" name="email" id="email" value="" />
            </td>
        </tr>

        <tr>
            <th>メールアドレス（確認）</th>
            <td>
                <input type="text" class="len400" name="c_email" id="c_email" value="" />
            </td>
        </tr>

        <tr>
            <th>権限</th>
            <td>
                <label><input type="radio" name="authority" id="authority-10" value="10" />管理者</label>
                <label><input type="radio" name="authority" id="authority-1" value="1" checked="checked"/>一般ユーザー</label>
            </td>
        </tr>

        <tr>
            <td colspan="2" style="text-align: center;">
                <input type="button" id="btn-primary" value="登録する" onclick="insertUser();" />
            </td>
        </tr>

    </table>

</form>
<script>
    // user_add
    $(function(){
        $("#name").on("keyup", function() {
        $('.search-null').remove();
        let userName = $('#name').val(); //検索ワードを取得

        if (!userName) {
            return false;
        }
        $.ajax({
            type: 'GET',
            url: '/seach/user/'+ userName, //後述するweb.phpのURLと同じ形にする
            data: {
                //ここはサーバーに贈りたい情報。今回は検索ファームのバリューを送りたい。
                'search_name':userName,
            },
            dataType: 'json', //json形式で受け取る

        }).done(function (data) {
            // 検索結果がなかったときの処理
            if (data.length === 0) {
                $('#btn-primary').prop('disabled', false);
            }else{
                $('#name').after('<p class="error search-null">既に存在するアカウント名です。</p>');
                $('#btn-primary').prop('disabled', true);
            }
        }).fail(function (XMLHttpRequest, textStatus, errorThrown){
    　　　//ajax通信がエラーのときの処理
            console.log(XMLHttpRequest.status);
            console.log(textStatus);
            console.log(errorThrown.message);
        })

        });
    })
    $(document).ready(function() {
        //validation
        $("#user_form").validate({

            errorElement:'p',

            rules: {
                password: {
                    required: true,
                    minlength: 7,
                    hankakueisu: true
                },
                c_password: {
                    equalTo: "#password"
                },

                email:{
                    email: true
                },
                c_email:{
                    equalTo: "#email"
                },

                name: {
                    required: true,
                    maxlength: 255,
                    hankakueisu: true
                },
                display_name: {
                    required: true,
                    hankakusymbol: true
                },

            },

            messages: {

                password: {
                    minlength: "7文字以上の半角英数文字で入力してください",
                    required: "パスワードは必須です"
                },
                c_password: {
                    equalTo: "パスワードと一致しません"
                },
                email:{
                    email: "無効なメールアドレスです"
                },
                name: {
                    required: "アカウントは必須です",
                    hankakueisu: "半角英数文字で入力してください"
                },
                display_name: {
                    required: "表示名は必須です",
                    hankakusymbol:"無効な文字が含まれてます"
                },
            }
    });

        jQuery.validator.addMethod("hankakueisu", function(value, element) {
            // allow any non-whitespace characters as the host part
            return this.optional( element ) || /^[a-zA-Z0-9]+$/.test( value );
        }, '半角英数文字で入力してください');
        jQuery.validator.addMethod("hankakusymbol", function(value, element) {
            // allow any non-whitespace characters as the host part
            return this.optional( element ) || /^[^ -/:-@\[-~]+$/.test( value );
        }, '無効な文字が含まれてます');


    });

    //新規アカウントを登録する
    function insertUser(){
        if(!$("#user_form").valid()){
            return false;
        }
        var formData = $("#user_form").serializeArray();
        var insertData = {};

        $.each(formData, function(i, element) {
            //console.log(element.name + ": " + element.value);

            insertData[element.name] = element.value;
        });

        //同一チェック
        if (insertData["password"] != insertData["c_password"]){
            alert("パスワードが異なります");
            return false;
        }

        if (insertData["email"] != insertData["c_email"]){
            alert("メールアドレスが異なります");
            return false;
        }

        $.ajax({
            type     : "POST",
            url      : "/store/user",
            data     : insertData,
            dataType : "json",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        }).done(function(data){
            alert('登録しました。');
            window.location.href = 'schedule_list.php';
        }).fail(function(XMLHttpRequest, textStatus, errorThrown){
            alert('登録に失敗しました。');
            //ajax通信がエラーのときの処理
            console.log(XMLHttpRequest.status);
            console.log(textStatus);
            console.log(errorThrown.message);
        });
    }


    function test(){
        var formData = $("#user_form").serializeArray();

        $.each(formData, function(i, element) {
            console.log(element.name + ": " + element.value)
        });
    }

</script>
@endsection
