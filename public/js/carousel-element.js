(function () {
  function initCarousel(container) {
    const track      = container.querySelector('.carousel-track')
    const slides     = container.querySelectorAll('.carousel-slide')
    const prevBtn    = container.querySelector('.prev-btn')
    const nextBtn    = container.querySelector('.next-btn')
    const indicators = container.querySelectorAll('.indicator')

    const autoPlay = container.dataset.autoplay === 'true'
    const interval = parseInt(container.dataset.interval) || 3000
    const total    = slides.length

    if (total <= 1) return  // 只有一張不需要輪播邏輯

    let current = 0
    let timer   = null

    // 切換到指定 index
    function goTo(index) {
      current = (index + total) % total
      track.style.transform = `translateX(-${current * 100}%)`

      indicators.forEach((dot, i) => {
        dot.classList.toggle('active', i === current)
      })
    }

    function next() { goTo(current + 1) }
    function prev() { goTo(current - 1) }

    // 自動播放
    function startAutoplay() {
      if (!autoPlay) return
      stopAutoplay()
      timer = setInterval(next, interval)
    }

    function stopAutoplay() {
      if (timer) { clearInterval(timer); timer = null }
    }

    // 按鈕事件
    if (prevBtn) prevBtn.addEventListener('click', () => { goTo(current - 1); startAutoplay() })
    if (nextBtn) nextBtn.addEventListener('click', () => { goTo(current + 1); startAutoplay() })

    // 指示器事件
    indicators.forEach((dot, i) => {
      dot.addEventListener('click', () => { goTo(i); startAutoplay() })
    })

    // 滑鼠停留時暫停
    container.addEventListener('mouseenter', stopAutoplay)
    container.addEventListener('mouseleave', startAutoplay)

    // 觸控支援（手機滑動）
    let touchStartX = 0
    container.addEventListener('touchstart', (e) => {
      touchStartX = e.touches[0].clientX
      stopAutoplay()
    }, { passive: true })

    container.addEventListener('touchend', (e) => {
      const diff = touchStartX - e.changedTouches[0].clientX
      if (Math.abs(diff) > 50) {
        diff > 0 ? next() : prev()
      }
      startAutoplay()
    }, { passive: true })

    // 啟動
    startAutoplay()
  }

  // 初始化頁面上所有輪播
  function initAll() {
    document.querySelectorAll('.carousel-container').forEach(initCarousel)
  }

  // DOM 載入後初始化
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initAll)
  } else {
    initAll()
  }
})()
