<?php require_once("config.php"); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Kelas</title>
    <!-- Bootstrap CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
</head>
<body class="p-4">
    <h1>Data Kelas</h1>

    <!-- Tombol untuk membuka modal -->
    <button type="button" class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#kelasModal">
        Tambah Kelas
    </button>

    <!-- Modal: Form Tambah/Edit Kelas -->
    <div class="modal fade" id="kelasModal" tabindex="-1" aria-labelledby="kelasModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="proses.kelas.php" method="post">
                    <input type="hidden" name="id" id="kelas_id" value="">
                    <div class="modal-header">
                        <h5 class="modal-title" id="kelasModalLabel">Tambah Kelas</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="nama" class="form-label">Nama Kelas</label>
                            <input type="text" name="nama" id="nama" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label d-block">Tingkat</label>
                            <div class="btn-group" role="group" aria-label="Tingkat">
                                <input type="radio" class="btn-check" name="tingkat" id="t1" value="10" autocomplete="off" checked>
                                <label class="btn btn-outline-secondary" for="t1">10</label>

                                <input type="radio" class="btn-check" name="tingkat" id="t2" value="11" autocomplete="off">
                                <label class="btn btn-outline-secondary" for="t2">11</label>

                                <input type="radio" class="btn-check" name="tingkat" id="t3" value="12" autocomplete="off">
                                <label class="btn btn-outline-secondary" for="t3">12</label>

                                <input type="radio" class="btn-check" name="tingkat" id="t4" value="13" autocomplete="off">
                                <label class="btn btn-outline-secondary" for="t4">13</label>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="jurusan_id" class="form-label">Jurusan</label>
                            <select name="jurusan_id" id="jurusan_id" class="form-select" required>
                                <?php
                                    $res_jrs = $db->prepare("SELECT * FROM jurusan ORDER BY nama");
                                    $res_jrs->execute(); 
                                    while ($row_jrs = $res_jrs->fetchObject()):
                                ?>
                                    <option value="<?= $row_jrs->id ?>"><?= $row_jrs->nama  ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button class="btn btn-primary" type="submit">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

<br>
    <table class="table table-bordered">
        <thead>
            <th>No.</th>
            <th>Nama Kelas</th>
            <th>Tingkat</th>
            <th>Jurusan</th>
            <th>Aksi</th>
        </thead>
        <tbody>
        <?php  
            $res_kls = $db->prepare("SELECT kelas.*, 
                                        kelas.nama AS nama_kelas,
                                        jurusan.nama AS nama_jurusan 
                                     FROM kelas
                                     LEFT JOIN jurusan ON jurusan.id = kelas.jurusan_id
                                     ");
            $res_kls->execute();
            $i = 0;
            while ($row_kls = $res_kls->fetchObject()) {
        ?>        
            <tr>
                <td><?= ++$i ?></td>
                <td><?= $row_kls->nama_kelas ?></td>
                <td><?= $row_kls->tingkat ?></td>
                <td><?= $row_kls->nama_jurusan ?></td>
                <td>
                    <button 
                        type="button" 
                        class="btn btn-warning btn-sm edit-btn"
                        data-id="<?= $row_kls->id ?>"
                        data-nama="<?= htmlspecialchars($row_kls->nama_kelas, ENT_QUOTES) ?>"
                        data-tingkat="<?= $row_kls->tingkat ?>"
                        data-jurusan="<?= $row_kls->jurusan_id ?>"
                    >Edit</button>

                    <a class="btn btn-danger btn-sm" href="proses.kelas.php?hapus=<?= $row_kls->id ?>" onclick="return confirm('Yakin hapus kelas <?= htmlspecialchars($row_kls->nama_kelas, ENT_QUOTES) ?> ?')">Hapus</a>
                </td>
            </tr>
        <?php } ?>
        </tbody>
    </table>

<script>
document.addEventListener('DOMContentLoaded', function(){
    var modalEl = document.getElementById('kelasModal');
    var bsModal = new bootstrap.Modal(modalEl);

    // Setup "Tambah Kelas" button to reset form
    var tambahBtn = document.querySelector('button[data-bs-target="#kelasModal"]');
    if (tambahBtn) {
        tambahBtn.addEventListener('click', function(){
            document.getElementById('kelas_id').value = '';
            document.getElementById('nama').value = '';
            var radio = document.querySelector('input[name="tingkat"][value="10"]');
            if (radio) radio.checked = true;
            document.getElementById('jurusan_id').selectedIndex = 0;
            document.getElementById('kelasModalLabel').textContent = 'Tambah Kelas';
            modalEl.querySelector('.modal-footer .btn-primary').textContent = 'Simpan';
        });
    }

    // Edit buttons
    document.querySelectorAll('.edit-btn').forEach(function(btn){
        btn.addEventListener('click', function(){
            document.getElementById('kelas_id').value = btn.dataset.id || '';
            document.getElementById('nama').value = btn.dataset.nama || '';
            var tingkat = btn.dataset.tingkat || '10';
            var tingkatInput = document.querySelector('input[name="tingkat"][value="'+tingkat+'"]');
            if (tingkatInput) tingkatInput.checked = true;
            var jurusanSelect = document.getElementById('jurusan_id');
            if (jurusanSelect && btn.dataset.jurusan) jurusanSelect.value = btn.dataset.jurusan;
            document.getElementById('kelasModalLabel').textContent = 'Edit Kelas';
            modalEl.querySelector('.modal-footer .btn-primary').textContent = 'Update';
            bsModal.show();
        });
    });
});
</script>

</body>
</html>