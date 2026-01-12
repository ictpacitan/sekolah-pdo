<?php 
    require_once("config.php");
    
    // handle delete request via query string ?hapus=ID
    if (isset($_GET['hapus'])) {
        $id = intval($_GET['hapus']);
        if ($id > 0) {
            $res_del = $db->prepare("DELETE FROM kelas WHERE id = ?");
            $res_del->execute([$id]);
        }
        header("Location: index.php"); exit;
    }

    // cek apakah ada pengiriman form
    if (isset($_POST['nama'])) {
        // ambil id jika ada (edit)
        $id = isset($_POST['id']) ? intval($_POST['id']) : 0;

        if ($id > 0) {
            // UPDATE
            $res = $db->prepare("
                UPDATE kelas 
                SET nama = ?, tingkat = ?, jurusan_id = ?
                WHERE id = ?
            ");
            $hasil = $res->execute([
                $_POST['nama'],
                $_POST['tingkat'],
                $_POST['jurusan_id'],
                $id
            ]);
            if ($hasil) {
                header("Location: index.php"); exit;
            } else {
                die("Ada kesalahan saat mengupdate data.");
            }
        } else {
            // INSERT
            $res = $db->prepare("
                INSERT INTO kelas (nama, tingkat, jurusan_id) 
                VALUES (?, ?, ?)
            ");
            $hasil = $res->execute([
                $_POST['nama'],
                $_POST['tingkat'],
                $_POST['jurusan_id'],
            ]);
            if ($hasil) {
                header("Location: index.php"); exit;
            } else {
                die("Ada kesalahan penyimpanan ke database.");
            }  
        }            
    }