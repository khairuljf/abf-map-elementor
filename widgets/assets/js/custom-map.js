jQuery(window).on("elementor/frontend/init", function () {
    elementorFrontend.hooks.addAction(
        "frontend/element_ready/hip-map-id.default",
        function ($scope, $) {

            window.addEventListener('load', global_initialize)
            var latLngArr = [];
            function global_initialize(){
                var mapElement = $scope.find(".hip-custom-map");
                var mapSettings = mapElement.data("settings");
                newMapData = renderNewMap(mapElement, mapSettings);
            }

            function renderNewMap(map,settings){

                let windowCurrentWIdht = window.innerWidth;
                let zoom  = settings["zoom_desktop"];
                if(windowCurrentWIdht<=1024){
                    zoom  = settings["zoom_tab"];
                }

                if(windowCurrentWIdht<=767){
                    zoom  = settings["zoom_mobile"];
                }

                let mapstyle = settings.mapstyle;
                let centerLat = settings.centerlat;
                let centerLong = settings.centerlong;
                let autoOpen = settings.automaticOpen;
                let fitBounds = settings.fitBounds;
                let map_zoom_control = settings.map_zoom_control   == 'yes'? true : false
                let zoom_in_double_click = settings.double_click_option   == 'yes'? true : false
                let zoom_in_while = settings.mouse_wheel_option == 'yes'  ? true : false
                let map_dragging = settings.map_dragging_option   == 'yes'? true : false


                let args = {
                    zoom: zoom,
                    mapstyle: mapstyle,
					zoomControl: map_zoom_control,
                    doubleClickZoom: zoom_in_double_click,
                    scrollWheelZoom: zoom_in_while,
                    dragging:map_dragging,
					closePopupOnClick: false, // No need to hide when click outer of close icon
                    center: { lat: centerLat, lng: centerLong }

                };

                let markers = $scope.find(".hip-pin-icon");

                var map = L.map('map', args);
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
                }).addTo(map);

                map.markers = [];
                var bounds = 'yes';
                // add markers
                markers.each(function (index) {
                    add_marker(jQuery(this),map, autoOpen);;
                });
                if ('yes' == fitBounds){
                    map.fitBounds(latLngArr,{ padding: [50, 50] });
                }
                L.tileLayer.provider(`${args.mapstyle}`).addTo(map);
                document.getElementsByClassName( 'leaflet-control-attribution' )[0].style.display = 'none';
                //  return map;
            }
            function add_marker(pin ,map,autoOpen){
                let lat = pin.attr("data-lat");
                let long = pin.attr("data-lng");
                let pin_icon = pin.attr("data-icon");
                let pin_url = pin.attr("data-url");
                let pin_title = pin.attr("data-title");
                let el_id = pin.attr("item_id");
                let LeafIcon = L.Icon.extend({
                    options: {
                        iconSize:     [25, 41],
                        iconAnchor:   [22, 41],
                        popupAnchor:  [-10, -44], // map popup position
                    }
                });



                let  hip_cs_icon = new LeafIcon({iconUrl: pin_icon});
                let pin_info_box = `<a  href="${pin_url}">${pin_title}</a>` ;


				//create marker
				if ('yes' == autoOpen){
					new L.marker([lat, long ], {icon: hip_cs_icon}).bindPopup(`${pin_info_box}`,{autoClose:false, className:`elementor-repeater-item-${el_id}`} ).addTo(map).openPopup()._popup._closeButton.addEventListener('click', (event) => event.preventDefault());
				}else {
					new L.marker([lat, long ], {icon: hip_cs_icon}).bindPopup(`${pin_info_box}`,  {className:`elementor-repeater-item-${el_id}`} ).addTo(map).on('click', ref_function) ;
				}


				// long lat push
                latLngArr.push([lat, long ]);

				function ref_function(){

					let closeButtons = document.querySelectorAll('.leaflet-popup-close-button');
					closeButtons.forEach((btn)=>{
						btn.addEventListener('click', function(e){
							e.preventDefault()
						})
					})
				}
			}
        })
});
