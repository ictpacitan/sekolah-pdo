<?php require_once("config.php"); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Kelas</title>
    <!-- Bootstrap CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <!-- jQuery CDN -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj3OJU5yExlq6GSYGSHk7tPFijrQlT0OdZ3Ciy985=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
</head>
<body class="p-4">
    <h1>Master Data Kelas</h1>

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
            // menyiapkan perintah SQL untuk pengambilan data kelas, join dengan jurusan
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
$(document).ready(function(){
    var modalEl = $('#kelasModal');
    var bsModal = new bootstrap.Modal(modalEl[0]);

    // Setup "Tambah Kelas" button to reset form
    $('button[data-bs-target="#kelasModal"]').on('click', function(){
        $('#kelas_id').val('');
        $('#nama').val('');
        $('input[name="tingkat"][value="10"]').prop('checked', true);
        $('#jurusan_id').prop('selectedIndex', 0);
        $('#kelasModalLabel').text('Tambah Kelas');
        modalEl.find('.modal-footer .btn-primary').text('Simpan');
    });

    // Edit buttons
    $(document).on('click', '.edit-btn', function(){
        var btn = $(this);
        $('#kelas_id').val(btn.data('id') || '');
        $('#nama').val(btn.data('nama') || '');
        var tingkat = btn.data('tingkat') || '10';
        $('input[name="tingkat"][value="' + tingkat + '"]').prop('checked', true);
        $('#jurusan_id').val(btn.data('jurusan') || '');
        $('#kelasModalLabel').text('Edit Kelas');
        modalEl.find('.modal-footer .btn-primary').text('Update');
        bsModal.show();
    });
});
</script>

</body>
</html>