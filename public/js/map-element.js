// map-element.js
// 放在 public/js/map-element.js

(function () {
  function initMap(container) {
    const lat     = parseFloat(container.dataset.lat)  || 25.033
    const lng     = parseFloat(container.dataset.lng)  || 121.565
    const zoom    = parseInt(container.dataset.zoom)   || 15
    const address = container.dataset.address || ''

    const map = window.L.map(container).setView([lat, lng], zoom)

    window.L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      attribution: '© OpenStreetMap contributors',
      maxZoom: 19
    }).addTo(map)

    const marker = window.L.marker([lat, lng]).addTo(map)

    if (address) {
      marker.bindPopup(address)
    }
  }

  function initAllMaps() {
    document.querySelectorAll('.map-display').forEach(function (container) {
      // 避免重複初始化
      if (container.dataset.initialized) return
      container.dataset.initialized = 'true'
      initMap(container)
    })
  }

  // 載入 Leaflet CSS + JS，載入完才初始化
  function loadLeaflet(callback) {
    if (window.L) {
      callback()
      return
    }

    // CSS
    var css = document.createElement('link')
    css.rel  = 'stylesheet'
    css.href = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css'
    document.head.appendChild(css)

    // JS
    var script  = document.createElement('script')
    script.src  = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js'
    script.onload = callback
    document.head.appendChild(script)
  }

  function boot() {
    // 有 .map-display 才載入 Leaflet，沒有地圖的頁面不載入
    if (!document.querySelector('.map-display')) return
    loadLeaflet(initAllMaps)
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', boot)
  } else {
    boot()
  }
})()
