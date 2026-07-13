/* =====================================================
   MAIN.JS — JavaScript Vanilla "Coating Cepat"
   Berisi:
   1. Inisialisasi AOS (animasi on-scroll)
   2. Counter statistik animasi (FITUR 4.1)
   3. Slider perbandingan Before-After drag handle (FITUR 4.1 & 4.6)
   4. Filter kategori galeri (FITUR 4.5)
   5. Swiper testimoni (FITUR 4.1 & 4.7)
   6. Kalkulator estimasi harga (FITUR 4.8)
   7. Validasi client-side form booking (FITUR 4.9)
   ===================================================== */

document.addEventListener('DOMContentLoaded', function () {

  /* ===== 1. AOS — animasi on-scroll ===== */
  if (typeof AOS !== 'undefined') {
    AOS.init({ duration: 700, once: true, offset: 60 });
  }

  /* ===== 2. COUNTER STATISTIK ANIMASI ===== */
  const counters = document.querySelectorAll('[data-counter]');
  if (counters.length) {
    const animate = (el) => {
      const target = parseFloat(el.dataset.counter);
      const isFloat = String(el.dataset.counter).includes('.');
      const dur = 1600;
      const start = performance.now();
      const step = (now) => {
        const p = Math.min((now - start) / dur, 1);
        const val = target * (1 - Math.pow(1 - p, 3)); // ease-out
        el.textContent = isFloat ? val.toFixed(1) : Math.round(val).toLocaleString('id-ID');
        if (p < 1) requestAnimationFrame(step);
      };
      requestAnimationFrame(step);
    };
    const io = new IntersectionObserver((entries) => {
      entries.forEach((e) => {
        if (e.isIntersecting) { animate(e.target); io.unobserve(e.target); }
      });
    }, { threshold: 0.4 });
    counters.forEach((c) => io.observe(c));
  }

  /* ===== 3. BEFORE-AFTER SLIDER (input range + clip-path) ===== */
  document.querySelectorAll('.ba-slider').forEach((slider) => {
    const range  = slider.querySelector('input[type=range]');
    const after  = slider.querySelector('.ba-after');
    const handle = slider.querySelector('.ba-handle');
    if (!range || !after || !handle) return;
    const update = () => {
      const v = range.value;
      after.style.clipPath = 'inset(0 0 0 ' + v + '%)';
      handle.style.left = v + '%';
    };
    range.addEventListener('input', update);
    update();
  });

  /* ===== 4. FILTER KATEGORI GALERI (JS Vanilla) ===== */
  const filterBtns = document.querySelectorAll('.filter-btn');
  const galleryItems = document.querySelectorAll('.gallery-item');
  filterBtns.forEach((btn) => {
    btn.addEventListener('click', () => {
      filterBtns.forEach((b) => b.classList.remove('active'));
      btn.classList.add('active');
      const cat = btn.dataset.filter;
      galleryItems.forEach((item) => {
        item.style.display = (cat === 'all' || item.dataset.category === cat) ? '' : 'none';
      });
    });
  });

  /* ===== 5. SWIPER TESTIMONI ===== */
  if (typeof Swiper !== 'undefined' && document.querySelector('.testi-swiper')) {
    new Swiper('.testi-swiper', {
      slidesPerView: 1,
      spaceBetween: 20,
      loop: true,
      autoplay: { delay: 4500 },
      pagination: { el: '.swiper-pagination', clickable: true },
      breakpoints: { 768: { slidesPerView: 2 }, 1100: { slidesPerView: 3 } }
    });
  }

  /* ===== 6. KALKULATOR ESTIMASI HARGA =====
     Rumus: harga dasar layanan x multiplier ukuran mobil */
  const calcForm = document.getElementById('calcForm');
  if (calcForm) {
    const sizeMultiplier = { S: 1.0, M: 1.15, L: 1.3, XL: 1.5 };
    const serviceSel = document.getElementById('calcService');
    const sizeSel    = document.getElementById('calcSize');
    const typeSel    = document.getElementById('calcType');
    const resultBox  = document.getElementById('calcResult');
    const priceEl    = document.getElementById('calcPrice');
    const bookBtn    = document.getElementById('calcBookBtn');

    const hitung = () => {
      const base = parseFloat(serviceSel.selectedOptions[0]?.dataset.price || 0);
      const mult = sizeMultiplier[sizeSel.value] || 1;
      if (!base || !typeSel.value) { resultBox.classList.add('d-none'); return; }
      const total = Math.round(base * mult / 1000) * 1000; // bulatkan ribuan
      priceEl.textContent = 'Rp ' + total.toLocaleString('id-ID');
      bookBtn.href = 'index.php?page=booking&service=' + serviceSel.value;
      resultBox.classList.remove('d-none');
    };
    [serviceSel, sizeSel, typeSel].forEach((el) => el.addEventListener('change', hitung));
  }

  /* ===== 7. VALIDASI CLIENT-SIDE FORM BOOKING ===== */
  const bookingForm = document.getElementById('bookingForm');
  if (bookingForm) {
    bookingForm.addEventListener('submit', function (e) {
      const phone = bookingForm.querySelector('[name=phone]');
      const digits = (phone.value || '').replace(/\D/g, '');
      const validWa = /^(08|628)\d+$/.test(digits) && digits.length >= 10 && digits.length <= 15;
      if (!validWa) {
        e.preventDefault();
        phone.classList.add('is-invalid');
        phone.focus();
        return false;
      }
      phone.classList.remove('is-invalid');
    });
  }

});
