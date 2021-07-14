@extends('layouts/app')
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

<!-- 2つめ
    <li itemprop="itemListElement" itemscope
    itemtype="https://schema.org/ListItem">
    <a itemprop="item" href="コンテンツ一覧画面">
        <span itemprop="name">コンテンツ管理</span>
    </a>
    <meta itemprop="position" content="2" />
</li>-->

<!-- 3つめ
    <li itemprop="itemListElement" itemscope
    itemtype="https://schema.org/ListItem">
    <a itemprop="item" href="子カテゴリーのURL">
        <span itemprop="name">子カテゴリー名</span>
    </a>
    <meta itemprop="position" content="3" />
</li>-->
</ol>
<main class="py-4">
    <div class="container">
        <div class="row justfy-content-center">
            <div class="col-md-8" style="margin: 0 auto; text-align: center;">
                <h3><a href="/users">ユーザー一覧画面</a></h3>
                <h3><a href="/contents">コンテンツ一覧画面</a></h3>
                <h3><a href="/schedule">スケジュール一覧画面</a></h3>
            </div>

        </div>
    </div>
</main>

@endsection
