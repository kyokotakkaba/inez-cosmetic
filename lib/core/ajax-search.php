<?php
	require_once "../../conf/function.php";

	header('Content-Type: application/json');

	if(!isset($_GET['search']) OR !isset($_GET['category'])){
		exit();
	}

	$search 	= saring($_GET['search']);
	$category 	= saring($_GET['category']);

	$result = array();
	$data = array();

	if(strpos($category, '[pisah]') == true){
		$pecah = explode('[pisah]', $category);
		$category = $pecah[0];
		$idData = $pecah[1];
	}

	if($category=='karyawan'){
		$q = "
				SELECT 
					id,
					nik,
					nama,
					tmpt_lahir,
					tgl_lahir
				FROM 
					karyawan
				WHERE 
					(
					nama LIKE '%$search%' 
				OR
					nik LIKE '%$search%' 
					)
				AND
					hapus = '0'
				LIMIT
					10
		";

		$e = mysqli_query($conn, $q);

		while($r = mysqli_fetch_assoc($e)){
			$id = strtoupper($r['id']);
			$judul = strtoupper($r['nik']).' - '.ucwords($r['nama']);
			$desk = ucwords($r['tmpt_lahir']).', '.tanggalKan($r['tgl_lahir']);
			$data[] = array("title" => trim($judul), "description" => trim($desk), "attrib" => trim($id));
		}
	}
	else if($category=='calon supervisor'){
		$q = "
				SELECT 
					id,
					nik,
					nama,
					tmpt_lahir,
					tgl_lahir

				FROM 
					karyawan

				WHERE 
					id 
				NOT IN 
					(
						SELECT
							id_karyawan
						FROM
							wilayah_supervisi
						WHERE
							hapus = '0'
						AND
							id_karyawan != '$idData'
					)
				AND
					(
					nama LIKE '%$search%' 
				OR
					nik LIKE '%$search%' 
					)
				AND
					hapus = '0'
				LIMIT
					10
		";

		$e = mysqli_query($conn, $q);

		while($r = mysqli_fetch_assoc($e)){
			$id = strtoupper($r['id']);
			$judul = strtoupper($r['nik']).' - '.ucwords($r['nama']);
			$desk = ucwords($r['tmpt_lahir']).', '.tanggalKan($r['tgl_lahir']);
			$data[] = array("title" => trim($judul), "description" => trim($desk), "attrib" => trim($id));
		}
	}
	else if($category=='produk'){
		$q = "
				SELECT 
					id,
					nama,
					deskripsi
					
				FROM 
					produk

				WHERE 
					nama LIKE '%$search%' 
				AND
					hapus = '0'

				LIMIT
					10
		";

		$e = mysqli_query($conn, $q);

		while($r = mysqli_fetch_assoc($e)){
			$id = strtoupper($r['id']);
			$judul = strtoupper($r['nama']);
			$desk = ucwords($r['deskripsi']);
			$data[] = array("title" => trim($judul), "description" => trim($desk), "attrib" => trim($id));
		}
	}
	else if($category=='wilayah'){
		$q = "
				SELECT 
					id,
					kode,
					nama

				FROM 
					wilayah

				WHERE
					hapus = '0'
				AND
					(
						kode LIKE '%$search%'
					OR
						nama LIKE '%$search%'
					)

				LIMIT
					10
		";

		$e = mysqli_query($conn, $q);

		while($r = mysqli_fetch_assoc($e)){
			$id = strtoupper($r['id']);
			$judul = strtoupper($r['nama']);
			$desk = ucwords($r['kode']);
			$data[] = array("title" => trim($judul), "description" => trim($desk), "attrib" => trim($id));
		}
	}
	else{
		echo "Undefined Category";
		exit();
	}

	$result['result'] = $data;

	echo json_encode($result);
?>
    
