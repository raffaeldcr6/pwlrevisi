/* RentVentures — interaksi ringan (tanpa framework) */
(function () {
  'use strict';

  const rupiah = (n) => 'Rp ' + Math.round(n).toLocaleString('id-ID');

  /* ---- Navbar scroll effect ---- */
  const nav = document.querySelector('.rv-nav');
  if (nav) {
    const onScroll = () => {
      nav.classList.toggle('scrolled', window.scrollY > 10);
    };
    window.addEventListener('scroll', onScroll, { passive: true });
    onScroll();
  }

  /* ---- Konfirmasi hapus / batal dengan SweetAlert2 ---- */
  document.addEventListener('submit', function (e) {
    const form = e.target.closest('form.js-confirm');
    if (!form || form.dataset.confirmed === '1') return;
    e.preventDefault();

    const proceed = () => { form.dataset.confirmed = '1'; form.submit(); };

    if (!window.Swal) {
      if (window.confirm(form.dataset.text || 'Lanjutkan?')) proceed();
      return;
    }
    Swal.fire({
      title: form.dataset.title || 'Yakin?',
      text: form.dataset.text || '',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonText: form.dataset.ok || 'Ya, lanjutkan',
      cancelButtonText: 'Batal',
      confirmButtonColor: '#A8432F',
      cancelButtonColor: '#66756B',
      reverseButtons: true,
      focusCancel: true,
      customClass: {
        popup: 'rounded-4',
        confirmButton: 'btn btn-sm px-4',
        cancelButton: 'btn btn-sm px-4',
      },
    }).then((r) => { if (r.isConfirmed) proceed(); });
  });

  /* ---- Nonaktifkan tombol submit setelah diklik (cegah kirim ganda) ---- */
  document.addEventListener('submit', function (e) {
    const form = e.target;
    if (e.defaultPrevented || form.classList.contains('js-confirm')) return;
    const btn = form.querySelector('button[type=submit][data-busy]');
    if (btn) {
      btn.disabled = true;
      btn.dataset.label = btn.innerHTML;
      btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>' + btn.dataset.busy;
    }
  });

  /* ---- Widget daftar bawaan di beranda ---- */
  const packing = document.getElementById('packing');
  if (packing) {
    const totalEl = document.getElementById('packing-total');
    const link = document.getElementById('packing-link');
    const base = link.dataset.base;
    const update = () => {
      let sum = 0; const ids = [];
      packing.querySelectorAll('input[type=checkbox]:checked').forEach((c) => {
        sum += Number(c.dataset.price); ids.push(c.value);
      });
      totalEl.textContent = rupiah(sum);
      link.href = ids.length ? base + '?' + ids.map((i) => 'pilih[]=' + i).join('&') : base;
    };
    packing.addEventListener('change', update);
    update();
  }

  /* ---- Form penyewaan: stepper jumlah + ringkasan harga ---- */
  const rentalForm = document.getElementById('rental-form');
  if (rentalForm) {
    const start = rentalForm.querySelector('[name=start_date]');
    const end = rentalForm.querySelector('[name=end_date]');
    const daysEl = document.getElementById('sum-days');
    const qtyEl = document.getElementById('sum-qty');
    const totalEl = document.getElementById('sum-total');
    const listEl = document.getElementById('sum-list');
    const dayMs = 86400000;

    const calc = () => {
      let days = 0;
      if (start.value && end.value) {
        days = Math.round((new Date(end.value) - new Date(start.value)) / dayMs) + 1;
      }
      const valid = days > 0;
      let qty = 0, perDay = 0; const lines = [];

      rentalForm.querySelectorAll('.pick-row').forEach((row) => {
        const input = row.querySelector('input[type=number]');
        const q = Math.max(0, parseInt(input.value, 10) || 0);
        if (q > 0) {
          qty += q; perDay += q * Number(row.dataset.price);
          lines.push('<li class="d-flex justify-content-between gap-2"><span>' + q + '&times; ' + row.dataset.name + '</span><span class="fw-semibold">' + rupiah(q * Number(row.dataset.price) * (valid ? days : 1)) + '</span></li>');
        }
      });
      if (daysEl) daysEl.textContent = valid ? days + ' hari' : 'Tanggal belum valid';
      if (qtyEl)  qtyEl.textContent = qty + ' unit';
      if (totalEl) totalEl.textContent = rupiah(valid ? perDay * days : 0);
      if (listEl)  listEl.innerHTML = lines.length ? lines.join('') : '<li class="text-stone">Belum ada alat dipilih.</li>';
    };

    rentalForm.addEventListener('click', (e) => {
      const b = e.target.closest('[data-step]');
      if (!b) return;
      const input = b.parentElement.querySelector('input');
      const max = Number(input.max) || 50;
      const next = (parseInt(input.value, 10) || 0) + Number(b.dataset.step);
      input.value = Math.min(max, Math.max(0, next));
      calc();
    });
    rentalForm.addEventListener('input', calc);
    if (start) start.addEventListener('change', () => {
      if (end.value < start.value) end.value = start.value;
      end.min = start.value;
      calc();
    });
    if (start && end) end.min = start.value;
    calc();
  }

  /* ---- Pratinjau foto saat upload ---- */
  const imgInput = document.getElementById('image-input');
  if (imgInput) {
    imgInput.addEventListener('change', () => {
      const f = imgInput.files[0];
      const prev = document.getElementById('image-preview');
      if (f && prev) { prev.src = URL.createObjectURL(f); prev.classList.remove('d-none'); }
    });
  }

  /* ---- Notifikasi count badge live ---- */
  const notifBadge = document.getElementById('notif-count');
  if (notifBadge && notifBadge.dataset.url) {
    const refresh = () => {
      fetch(notifBadge.dataset.url)
        .then(r => r.json())
        .then(d => {
          if (d.count > 0) {
            notifBadge.textContent = d.count > 99 ? '99+' : d.count;
            notifBadge.style.display = '';
          } else {
            notifBadge.style.display = 'none';
          }
        })
        .catch(() => {});
    };
    refresh();
    setInterval(refresh, 60000); // refresh tiap menit
  }

  /* ---- Auto-dismiss flash messages ---- */
  document.querySelectorAll('.alert-autodismiss').forEach((el) => {
    setTimeout(() => {
      el.style.transition = 'opacity .4s ease, margin-top .4s ease';
      el.style.opacity = '0';
      el.style.marginTop = '-' + el.offsetHeight + 'px';
      setTimeout(() => el.remove(), 400);
    }, 5000);
  });

  /* ---- Reveal on scroll ---- */
  if ('IntersectionObserver' in window) {
    const observer = new IntersectionObserver((entries) => {
      entries.forEach(e => {
        if (e.isIntersecting) {
          e.target.style.opacity = '1';
          e.target.style.transform = 'translateY(0)';
          observer.unobserve(e.target);
        }
      });
    }, { threshold: 0.1 });

    document.querySelectorAll('.reveal').forEach(el => {
      el.style.opacity = '0';
      el.style.transform = 'translateY(20px)';
      el.style.transition = 'opacity .4s ease, transform .4s ease';
      observer.observe(el);
    });
  }
})();
