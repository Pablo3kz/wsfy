(function ($) {


    function getMapTypeId(type) {
        var mapTypeId = google.maps.MapTypeId.ROADMAP;
        switch (type) {
            case "1":
                mapTypeId = google.maps.MapTypeId.HYBRID;
                break;
            case "3":
                mapTypeId = google.maps.MapTypeId.SATELLITE;
                break;
            case "4":
                mapTypeId = google.maps.MapTypeId.TERRAIN;
                break;
            default:
                mapTypeId = google.maps.MapTypeId.ROADMAP;
                break;
        }
        return mapTypeId;
    }

    $(".wyde-gmaps").each(function () {

        var el = this;

        var options = $.parseJSON(decodeURIComponent($(".wyde_gmaps_field", el).val()));

        var settings = $.extend(true, {
            position: { lat: 37.6, lng: -95.665 },
            address: "",
            zoom: 8,
            type: 2
        }, options);


        function update() {
            $(".wyde_gmaps_field", el).val(encodeURIComponent(JSON.stringify(settings)));
        }

        var position;
        if (settings) {
            position = new google.maps.LatLng(settings.position.lat, settings.position.lng);
            $(".map-address", el).val(settings.address);
            $(".map-type", el).val(settings.type);
            $(".map-zoom", el).val(settings.zoom);
        }


        var mapTypeId = getMapTypeId(settings.type);

        var mapCanvas = $(".gmaps-canvas", el).get(0);
        var mapOptions = {
            zoom: settings.zoom,
            center: position,
            mapTypeId: mapTypeId
        };

        var map = new google.maps.Map(mapCanvas, mapOptions);

        var marker = new google.maps.Marker({
            position: position,
            title: "Drag & Drop marker",
            map: map,
            animation: google.maps.Animation.DROP,
            draggable: true
        });

        google.maps.event.addListener(marker, "dragend", function () {
            position = marker.getPosition();
            settings.position = { lat: position.lat(), lng: position.lng() };
            update();
        });


        $(".map-address", el).change(function () {

            var address = $(this).val();
            settings.address = address;
            update();

            if (!address) return;
            var geocoder = new google.maps.Geocoder();
            geocoder.geocode({ address: address }, function (responses, status) {
                if (status === google.maps.GeocoderStatus.OK && responses.length > 0 && responses[0].geometry) {
                    position = responses[0].geometry.location;
                    settings.position = { lat: position.lat(), lng: position.lng() };
                    map.setCenter(position);
                    marker.setPosition(position);
                    update();
                } else {
                    //console.log("Cannot determine address at this location.");
                }
            });
        });

        $(".map-type", el).change(function () {
            settings.type = $(this).val();
            var mapTypeId = getMapTypeId(settings.type);
            map.setMapTypeId(mapTypeId);
            update();
        });

        $(".map-zoom", el).change(function () {
            settings.zoom = parseInt($(this).val());
            map.setZoom(settings.zoom);
            map.setCenter(position);
            update();
        });

    });

})(jQuery);