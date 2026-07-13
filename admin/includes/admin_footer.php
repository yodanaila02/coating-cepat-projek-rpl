<?php
/**
 * =====================================================
 * FOOTER ADMIN PANEL + script CDN
 * (jQuery, Bootstrap4, AdminLTE, DataTables, SweetAlert2, Chart.js)
 * =====================================================
 */
?>
    </div></section>
  </div>

  <footer class="main-footer">
    <small>&copy; <?= date('Y') ?> <?= e(setting($db, 'site_name', 'Coating Cepat')) ?> — Admin Panel</small>
  </footer>
</div>

<script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap4.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
/* Inisialisasi DataTables (search + pagination) untuk semua tabel .datatable */
$(function () {
  $('.datatable').DataTable({
    language: {
      search: 'Cari:', lengthMenu: 'Tampil _MENU_ data', info: 'Menampilkan _START_-_END_ dari _TOTAL_',
      paginate: { previous: '&laquo;', next: '&raquo;' }, zeroRecords: 'Data tidak ditemukan', infoEmpty: 'Tidak ada data'
    }
  });

  /* Konfirmasi SweetAlert2 untuk semua form dengan class .confirm-form
     (hapus / approve / reject / tandai selesai) */
  $(document).on('submit', 'form.confirm-form', function (e) {
    var form = this;
    if (form.dataset.confirmed === '1') return;
    e.preventDefault();
    Swal.fire({
      title: form.dataset.title || 'Anda yakin?',
      text: form.dataset.text || '',
      icon: form.dataset.icon || 'warning',
      showCancelButton: true,
      confirmButtonColor: '#D98E32',
      cancelButtonText: 'Batal',
      confirmButtonText: 'Ya, lanjutkan'
    }).then(function (res) {
      if (res.isConfirmed) { form.dataset.confirmed = '1'; form.submit(); }
    });
  });
});
</script>
</body>
</html>
