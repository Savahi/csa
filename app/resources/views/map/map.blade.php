
	<div style='width:100%; margin:0px; box-sizing:border-box; padding:8px;' id='myMapContainer'>
    	<div id="myMap" style='width:100%; margin:0px; padding:0px; height:480px; border:1px dotted gray; box-sizing:border-box;'></div>
	</div>

    <div id="popup" class="ol-popup" style='display:none;'>
        <a href="#" id="popup-closer" class="ol-popup-closer"></a>
        <div id="popup-content"></div>
    </div>

    <script src="https://cdn.rawgit.com/openlayers/openlayers.github.io/master/en/v5.3.0/build/ol.js"></script>
	<script src="/js/myrequest.js"></script>

    <script>
		var _myMap = null;
		var _myMapContainer = null;
		@isset( $params ) 
		var _myMapParams = {!!$params!!};
		@else
		var _myMapParams = null;
		@endisset

		window.onload = function () {
	        _myMap = document.getElementById('myMap');
	        _myMapContainer = document.getElementById('myMapContainer');
			_myMap.style.height = Math.floor(window.innerHeight * 0.8);
			_myMapContainer.style.height = Math.floor(window.innerHeight * 0.8);

			let request = "/locations";
			if( _myMapParams !== null ) {
				request += "/" + _myMapParams;
			}
			myRequest( request, myMap );
		};

	function myMap(data) {

		let mapObjects = [];
		let minLat=null, maxLat=null, minLon=null, maxLon=null;
		
		for( let i = 0 ; i < data.length ; i++ ) {
			let url; 
			if( data[i].type == 'farm' ) {	
				url = '/farm/';
			} else if( data[i].type == 'delivery_point' ) {
				url = '/delivery_point/';
			} else {
				continue; 
			}
			mapObjects.push( { lat:data[i].latitude, lon:data[i].longitude, title:data[i].title, id:data[i].id, url:url } );			
			if( i == 0 ) {
				minLat = data[i].latitude;
				maxLat = data[i].latitude;
				minLon = data[i].longitude;
				maxLon = data[i].longitude;
			} else {
				if( data[i].latitude < minLat ) {
					minLat = data[i].latitude;
				}
				if( data[i].latitude > maxLat ) {
					maxLat = data[i].latitude;
				}
				if( data[i].longitude < minLon ) {
					minLon = data[i].longitude;
				}
				if( data[i].longitude > maxLon ) {
					maxLon = data[i].longitude;
				}
			}	
		}
		let midLat = minLat + (maxLat - minLat) / 2.0;
		let midLon = minLon + (maxLon - minLon) / 2.0;

        var attribution = new ol.control.Attribution({
            collapsible: false
        });
		//console.log(`midLon=${midLon}, midLat=${midLat}`);
                                                                                   
        let map = new ol.Map({
            controls: ol.control.defaults({ attribution: false }).extend([attribution]),
            layers: [
                new ol.layer.Tile({
                    source: new ol.source.OSM()
                })
            ],
            target: 'myMap',
            view: new ol.View({                                                    
                center: ol.proj.fromLonLat([midLon, midLat]),
				//center: ol.proj.fromLonLat([36.73, 55.14]),
                maxZoom: 18,
                zoom: 7
            })
        });

		let features = [];
		for( let i = 0 ; i < mapObjects.length ; i++ ) {
			let feature = new ol.Feature({
            	geometry: new ol.geom.Point( ol.proj.fromLonLat( [mapObjects[i].lon, mapObjects[i].lat]) )
            });
			features.push(feature); 
		}

        let layer = new ol.layer.Vector({
            source: new ol.source.Vector({
                features: features // [ new ol.Feature({geometry: new ol.geom.Point(ol.proj.fromLonLat([mapObjects[0].lon, mapObjects[0].lat]))})]
            })
        });
        map.addLayer(layer);

		for( let i = 0 ; i < mapObjects.length ; i++ ) {
			//console.log('mapObjects[i].lon, mapObjects[i].lat, i');
			let divPopup = document.createElement('div');
			divPopup.className='ol-popup';
			let divPopupCloser = document.createElement('a');
			divPopupCloser.href='#';
			divPopupCloser.id='ol-popup-closer-' + i;
			divPopupCloser.className	= 'ol-popup-closer';
			let divPopupContent = document.createElement('div');
			divPopupContent.id = 'ol-popup-content-' + i;					
			divPopup.appendChild(divPopupCloser);
			divPopup.appendChild(divPopupContent);
	        let overlay = new ol.Overlay({
				position: ol.proj.fromLonLat([mapObjects[i].lon, mapObjects[i].lat]),
				positioning: 'center-center',
    	        element: divPopup,
        	    autoPan: true,
            	autoPanAnimation: {
                	duration: 250
            	}
        	});
        	map.addOverlay(overlay);
	        divPopupCloser.onclick = function () {
            	overlay.setPosition(undefined);
            	divPopupCloser.blur();
            	return false;
        	};
	        divPopupContent.innerHTML = `<a href='${mapObjects[i].url}${mapObjects[i].id}' target=_blank>${mapObjects[i].title}</a>`;
			//let c = new ol.proj.fromLonLat([mapObjects[i].lon, mapObjects[i].lat]);
	        //overlay.setPosition( c );
			console.log(mapObjects[i].lon, mapObjects[i].lat, i);
		}

		/*
        var container = document.getElementById('popup');
        var content = document.getElementById('popup-content');
        var closer = document.getElementById('popup-closer');

        var overlay = new ol.Overlay({
            element: container,
            autoPan: true,
            autoPanAnimation: {
                duration: 250
            }
        });
        map.addOverlay(overlay);

        closer.onclick = function () {
            overlay.setPosition(undefined);
            closer.blur();
            return false;
        };

        map.on('singleclick', function (event) {
            if (map.hasFeatureAtPixel(event.pixel) === true) {
                var coordinate = event.coordinate;

                content.innerHTML = '<b>Hello world!</b><br />I am a popup.';
                overlay.setPosition(coordinate);
            } else {
                overlay.setPosition(undefined);
                closer.blur();
            }
        });
        */
        //content.innerHTML = `<a href='/${mapObjects[0].url}/${mapObjects[0].id}'>${mapObjects[0].title}</a>`;
        //overlay.setPosition(ol.proj.fromLonLat([mapObjects[0].lon, mapObjects[0].lat]));
	}

    </script>
