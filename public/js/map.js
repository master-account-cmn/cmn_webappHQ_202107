

    var map, infoWindow;
    var directionsDisplay;
    var directionsService;
    var pos;
    var begin;
    var waypoints;
    var end;

    // function point(){
    //     if($("#s_point").val()=='' || $("#e_point").val()==''){
    //         alert('選択されていません。');
    //         return false;
    //     }else{
    //         begin = new google.maps.LatLng($('#s_point option:selected').data('lat'),$('#s_point option:selected').data('lng'));
    //         end = new google.maps.LatLng($('#e_point option:selected').data('lat'),$('#e_point option:selected').data('lng'));
    //         waypoints = [];
    //         if($("#w_point").val()!==''){
    //             waypoints.push({ location: new google.maps.LatLng($('#w_point option:selected').data('lat'),$('#w_point option:selected').data('lng')) });
    //         }
    //         calcRoute();
    //     }
    // }
    function waypoints_make(point){
        waypoints = []; //初期化
        if(!point.length == 0){
            for(i=0;i<point.length;i++){
                console.log(point[i]);
                waypoints.push({ location: new google.maps.LatLng(point[i]['lat'],point[i]['lng']) });
            }
            calcRoute();
        }else{
            initialize();
        }
    }
    function ajax_route(){
        var now_30 = new Date(+new Date() + (30 * 60 * 1000));
        var Hour = now_30.getHours();
        var Min = now_30.getMinutes();
        var time_ago30 = Hour + ':' + Min;

        var now_60 = new Date(+new Date() + (60 * 60 * 1000));
        var Hour = now_60.getHours();
        var Min = now_60.getMinutes();
        var time_ago60 = Hour + ':' + Min;

        $.ajax({
            type     : "GET",
            url      : "../ride/map",
            data     : {'start_time':time_ago30,'end_time':time_ago60},
            dataType : "json",
            headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        }).done(function(data){
            if(data == false){
                alert('目的地がありません。')
            }
            waypoints_make(data);

        }).fail(function(XMLHttpRequest, status, e){
            alert(e);
        });
    }

    function initialize() {
        // インスタンス[geocoder]作成
        var geocoder = new google.maps.Geocoder();
        if(begin == null){
            var begin = new google.maps.LatLng('33.590188' , '130.420685');
        }
        geocoder.geocode({
            // 起点のキーワード
            'latLng': begin
            // 'address': begin

        }, function(result, status) {
            if (status == google.maps.GeocoderStatus.OK) {
                // 中心点を指定
                // オプション
                var myOptions = {
                    zoom: 14,
                    center: result[0].geometry.location,
                    scrollwheel: true,     // ホイールでの拡大・縮小
                    mapTypeId: google.maps.MapTypeId.ROADMAP,
                };

                // 場所
                $('#begin').text(begin);
                $('#end').text(end);

                // #map_canvasを取得し、[mapOptions]の内容の、地図のインスタンス([map])を作成する
                map = new google.maps.Map(document.getElementById('map'), myOptions);
                getLocation();


                // 経路を取得
                directionsDisplay = new google.maps.DirectionsRenderer();
                directionsDisplay.setMap(map);
                directionsDisplay.setPanel(document.getElementById('directionsPanel'));     // 経路詳細

            } else {
                alert('取得できませんでした…');
            }
        });
    }
    // 現在地取得
    function getLocation(){
        infoWindow = new google.maps.InfoWindow();
        // Try HTML5 geolocation.
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
              (position) => {
                pos = {
                  lat: position.coords.latitude,
                  lng: position.coords.longitude,
                };
                infoWindow.setPosition(pos);
                infoWindow.setContent("現在地");
                infoWindow.open(map);
                map.setCenter(pos);
              },
              () => {
                handleLocationError(true, infoWindow, map.getCenter());
              }
            );
          } else {
            // Browser doesn't support Geolocation
            handleLocationError(false, infoWindow, map.getCenter());
          }
    }
    // error
    function handleLocationError(browserHasGeolocation, infoWindow, pos) {
        infoWindow.setPosition(pos);
        infoWindow.setContent(
          browserHasGeolocation
            ? "Error: The Geolocation service failed."
            : "Error: Your browser doesn't support geolocation."
        );
        infoWindow.open(map);
      }
    // ルート取得
    function calcRoute() {
        var request = {
            origin: pos,        // 開始地点(現在地)
            destination: end = new google.maps.LatLng('33.590188' , '130.420685'),     // 終了地点(博多駅)
            waypoints: waypoints, // 経由地点
            optimizeWaypoints:true, //ルートの最適化
            travelMode: google.maps.TravelMode.DRIVING,     // [自動車]でのルート
            avoidHighways: true,        // 高速道路利用フラグ
        };

        // インスタンス作成
        directionsService = new google.maps.DirectionsService();

        directionsService.route(request, function(response, status) {
            if (status == google.maps.DirectionsStatus.OK) {
                directionsDisplay.setDirections(response);
            } else {
                alert('ルートが見つかりませんでした…');
            }
        });
    }
    // キック
    // google.maps.event.addListener(window, "load", initialize());


