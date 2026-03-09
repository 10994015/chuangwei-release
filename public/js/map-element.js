// public/js/map-element.js
// Google Maps 版本，對應 Vue 的 MapElement.vue 邏輯

(function () {
  'use strict'

  // ── 等待 Google Maps bootstrap 可用 ──────────────────────────────
  function waitForGoogleMaps(resolve) {
    if (window.google && window.google.maps && window.google.maps.importLibrary) {
      resolve()
    } else {
      setTimeout(function () { waitForGoogleMaps(resolve) }, 50)
    }
  }

  // ── 載入 Google Maps script（loading=async 官方推薦）─────────────
  function loadGoogleMaps() {
    return new Promise(function (resolve, reject) {
      // 已載入
      if (window.google && window.google.maps && window.google.maps.importLibrary) {
        return resolve()
      }
      // script 已存在但還未就緒
      if (document.getElementById('google-maps-script')) {
        return waitForGoogleMaps(resolve)
      }

      var apiKey = window.__GOOGLE_MAPS_API_KEY || ''
      var script = document.createElement('script')
      script.id   = 'google-maps-script'
      script.src  = 'https://maps.googleapis.com/maps/api/js?key=' + apiKey + '&loading=async'
      script.async = true
      script.defer = true
      script.onload = function () { waitForGoogleMaps(resolve) }
      script.onerror = function () { reject(new Error('Google Maps 載入失敗')) }
      document.head.appendChild(script)
    })
  }

  // ── 初始化單一地圖容器 ────────────────────────────────────────────
  async function initMap(container) {
    var lat     = parseFloat(container.dataset.lat)  || 25.033
    var lng     = parseFloat(container.dataset.lng)  || 121.565
    var zoom    = parseInt(container.dataset.zoom)   || 15
    var address = container.dataset.address          || ''

    try {
      // 與 Vue 版相同的三個 library
      var mapsLib    = await window.google.maps.importLibrary('maps')
      var markerLib  = await window.google.maps.importLibrary('marker')
      var geocodeLib = await window.google.maps.importLibrary('geocoding')

      var map = new mapsLib.Map(container, {
        center:               { lat: lat, lng: lng },
        zoom:                 zoom,
        mapId:                'DEMO_MAP_ID',      // AdvancedMarkerElement 必要
        mapTypeControl:       false,
        streetViewControl:    false,
        fullscreenControl:    true,
        zoomControl:          true,
      })

      var marker = new markerLib.AdvancedMarkerElement({
        position: { lat: lat, lng: lng },
        map:      map,
        title:    address,
      })

      if (address) {
        var infoWindow = new mapsLib.InfoWindow({
          content: '<div style="font-size:14px;padding:4px 8px">' + address + '</div>'
        })
        marker.addListener('gmp-click', function () {
          infoWindow.open(map, marker)
        })

        // 沒有提供座標時，用地址 geocoding
        if (!container.dataset.lat || !container.dataset.lng ||
            container.dataset.lat === '0' || container.dataset.lng === '0') {
          var geocoder = new geocodeLib.Geocoder()
          geocoder.geocode({ address: address }, function (results, status) {
            if (status === 'OK' && results[0]) {
              var loc = results[0].geometry.location
              map.setCenter(loc)
              marker.position = loc
            } else {
              console.warn('[map-element] Geocoding 失敗:', status)
            }
          })
        }
      }

    } catch (e) {
      console.error('[map-element] 初始化失敗:', e)
    }
  }

  // ── 初始化頁面上所有地圖 ──────────────────────────────────────────
  function initAllMaps() {
    document.querySelectorAll('.map-display').forEach(function (container) {
      if (container.dataset.initialized) return
      container.dataset.initialized = 'true'
      initMap(container)
    })
  }

  // ── 入口 ──────────────────────────────────────────────────────────
  function boot() {
    if (!document.querySelector('.map-display')) return

    loadGoogleMaps()
      .then(function () { initAllMaps() })
      .catch(function (e) { console.error(e.message) })
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', boot)
  } else {
    boot()
  }
})()
