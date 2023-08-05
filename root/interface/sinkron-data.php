<?php
    session_start();
    $appSection = 'root';

    if(empty($_SESSION['idPengguna'])){
        echo "SESSION EXPIRED";
        exit();
    }

    $jenisPengguna = $_SESSION['jenisPengguna'];
    if($jenisPengguna !== $appSection){
        echo "INVALID USER";
        exit();
    }

    $fromHome = '../../';
    require_once $fromHome."conf/function.php";


    $qSelect ="
    SELECT b.id idB, b.no, b.nama namaB,karyawan.nama nama_karyawan, karyawan.id user_id, karyawan.nik , wilayah.nama nama_wilayah,
( SELECT COUNT(id) FROM materi WHERE id_bahasan = idB AND hapus = '0' AND ( id_tingkat_belajar = 'semua' OR id_tingkat_belajar = '821A93B6-A91AF3CF-0999B3-0CCDB5F' OR id_tingkat_belajar = 'BFB620D1-1C6CC96E-B2DD0F9' ) ) jmlMateri, 
COUNT(l.id_materi) as jumlah_baca,
k.nama namaK, ADDTIME(CONVERT(l.tanggal, DATETIME), l.jam) as terakhir_buka 
FROM materi_kelompok_bahasan b 
LEFT JOIN materi_kelompok k ON b.id_kelompok = k.id 
LEFT JOIN ( SELECT id_bahasan , id_materi, id_karyawan, tanggal,jam FROM karyawan_belajar_materi WHERE hapus = '0' GROUP BY id_materi , id_karyawan  ORDER BY id_bahasan ASC, tanggal DESC, jam DESC) l ON l.id_bahasan = b.id 
LEFT JOIN karyawan ON karyawan.id = l.id_karyawan 
LEFT JOIN wilayah ON wilayah.id = karyawan.id_wil

WHERE b.hapus = '0' 
GROUP BY l.id_bahasan, l.id_karyawan ORDER BY user_id, b.no ASC;
    ";

    $eSelect = mysqli_query($conn, $qSelect);
    $c = mysqli_num_rows($eSelect);

    $q ="INSERT INTO laporan_pembelajaran (user_id,nip,area,id_modul,nama_modul,jumlah_modul,selesai_lembar,terakhir_dibuka) VALUES ";  
    while ($d = mysqli_fetch_assoc($eSelect)) {
        if($d['user_id']!=''){
            $q = $q."('".$d['user_id']."','".$d['nik']."', '".$d['nama_wilayah']."', '".$d['idB']."', '".$d['namaB']."', '".$d['jmlMateri']."', '".$d['jumlah_baca']."', '".$d['terakhir_buka']."'),";
        }
    }
    $q = substr($q, 0, -1);
    $q = $q." ON DUPLICATE KEY UPDATE selesai_lembar=VALUES(selesai_lembar), terakhir_dibuka=VALUES(terakhir_dibuka);";

    $e = mysqli_query($conn, $q);

    $qDate = "UPDATE sync_date SET waktu = '".date("Y-m-d H:i:s")."' WHERE nama = 'laporan pembelajaran'";
    echo $qDate;
    $eDate = mysqli_query($conn, $qDate); 
?>