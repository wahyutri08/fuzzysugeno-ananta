<?php
if (basename(__FILE__) == basename($_SERVER['SCRIPT_FILENAME'])) {
    header('HTTP/1.1 404 Forbidden');
    include("errors/404.html");
    exit();
}
$db = mysqli_connect("localhost", "root", "", "dev-fuzzy-ananta");
date_default_timezone_set('Asia/Jakarta');


// Base URL aplikasi
define("BASEURL", "http://localhost/fuzzysugeno-ananta/");

// Fungsi helper base_url
function base_url($path = "")
{
    return BASEURL . $path;
}

function query($query)
{
    global $db;
    $result = mysqli_query($db, $query);
    $rows = [];

    // Periksa apakah query berhasil dieksekusi
    if ($result) {
        if (mysqli_num_rows($result) > 0) {
            // Loop melalui hasil query
            while ($row = mysqli_fetch_assoc($result)) {
                $rows[] = $row; // Menambahkan baris hasil ke dalam array $rows
            }
        }
    } else {
        echo "Error: " . mysqli_error($db);
    }

    return $rows;
}

function addUser($data)
{
    global $db;

    $username = strtolower(stripcslashes($data["username"]));
    $email = strtolower(stripslashes($data["email"]));
    $name = ucfirst(stripslashes($data["name"]));
    $password = mysqli_real_escape_string($db, $data["password"]);
    $password2 = mysqli_real_escape_string($db, $data["password2"]);
    $role = htmlspecialchars($data["role"]);
    $status = htmlspecialchars($data["status"]);
    $created_at = date('Y-m-d H:i:s');


    //  Upload Gambar
    $avatar = upload();
    if ($avatar === -1) {
        return -3;
    }

    $result = mysqli_query($db, "SELECT * FROM users WHERE username = '$username'");

    if (mysqli_fetch_assoc($result)) {
        // Jika Nama Username Sudah Ada
        return -1;
    }

    if ($password !== $password2) {
        // Password 1 tidak sesuai dengan password 2
        return -2;
    }

    $password = password_hash($password, PASSWORD_DEFAULT);

    mysqli_query($db, "INSERT INTO users 
    (username, name, email, password, avatar, status, role, created_at) 
    VALUES 
    ('$username','$name', '$email', '$password', '$avatar', '$status', '$role', '$created_at')");
    return mysqli_affected_rows($db);
}

function editUsers($data)
{
    global $db;
    $id = ($data["id"]);
    $username = strtolower(stripslashes($data["username"]));
    $name = ucfirst(stripcslashes($data["name"]));
    $email = strtolower(stripslashes($data["email"]));
    $password = mysqli_real_escape_string($db, $data["password"]);
    $password2 = mysqli_real_escape_string($db, $data["password2"]);
    $avatarLama = htmlspecialchars($data["avatarLama"]);
    $role = htmlspecialchars($data["role"]);
    $status = htmlspecialchars($data["status"]);
    // $usernameLama = htmlspecialchars($data["username"]);

    // Cek apakah user pilih avatar baru atau tidak
    if ($_FILES['avatar']['error'] === 4) {
        $avatar = $avatarLama;
    } else {
        $avatar = upload();
        if ($avatar === -1) {
            // Kesalahan Jika Bukan Gambar
            return -1;
        } elseif ($avatar === -2) {
            // Kesalahan Ukuran Terlalu Besar
            return -2;
        }
    }

    if ($password !== $password2) {
        // Password 1 tidak sesuai dengan password 2
        return -3;
    }

    $password = password_hash($password, PASSWORD_DEFAULT);
    $query = "UPDATE users SET 
        username = '$username', 
        name = '$name', 
        email = '$email',
        avatar = '$avatar',
        status = '$status',
        role = '$role' WHERE id = $id";
    mysqli_query($db, $query);

    return mysqli_affected_rows($db);
}

function deleteUsers($id)
{
    global $db;
    mysqli_query($db, "DELETE FROM users WHERE id = $id");
    return mysqli_affected_rows($db);
}


function editProfile($data)
{
    global $db;
    $id = ($data["id"]);
    $name = ucfirst(stripcslashes($data["name"]));
    $email = strtolower(stripslashes($data["email"]));
    $avatarLama = htmlspecialchars($data["avatarLama"]);

    // Cek apakah user pilih avatar baru atau tidak
    if ($_FILES['avatar']['error'] === 4) {
        $avatar = $avatarLama;
    } else {
        $avatar = uploadPhotoProfile();
        if ($avatar === -1) {
            // Kesalahan Jika Bukan Gambar
            return -1;
        } elseif ($avatar === -2) {
            // Kesalahan Jika Ukuran Terlalu Besar
            return -2;
        }
    }

    $query = "UPDATE users SET 
        name = '$name',  
        email = '$email',
        avatar = '$avatar' WHERE id = $id";
    mysqli_query($db, $query);

    return mysqli_affected_rows($db);
}

function changePassword($data)
{
    global $db;
    $id = ($data["id"]);
    $password = mysqli_real_escape_string($db, $data["password"]);
    $password2 = mysqli_real_escape_string($db, $data["password2"]);

    if ($password !== $password2) {
        return -1;
    }

    $password = password_hash($password, PASSWORD_DEFAULT);
    $query = "UPDATE users SET 
    password = '$password' WHERE id = $id";
    mysqli_query($db, $query);

    return mysqli_affected_rows($db);
}


function upload()
{

    $namaFile = $_FILES['avatar']['name'];
    $ukuranFiles = $_FILES['avatar']['size'];
    $error = $_FILES['avatar']['error'];
    $tmpName = $_FILES['avatar']['tmp_name'];

    // Cek apakah yang diupload adalah gambar
    $ekstensiAvatarValid = ['', 'jpg', 'jpeg', 'png'];
    $ekstensiAvatar = explode('.', $namaFile);
    $ekstensiAvatar = strtolower(end($ekstensiAvatar));
    if (!in_array($ekstensiAvatar, $ekstensiAvatarValid)) {
        // Jika Avatar Bukan Gambar
        return -1;
    }

    if ($ukuranFiles > 10000000) {
        // Cek jika ukuran terlalu besar
        return -2;
    }

    // Gambar Siap Upload
    // generate nama gambar baru

    $namaFileBaru = uniqid();
    $namaFileBaru .= '.';
    $namaFileBaru .= $ekstensiAvatar;

    move_uploaded_file($tmpName, '../assets/dist/img/profile/' . $namaFileBaru);

    return $namaFileBaru;
}

function uploadPhotoProfile()
{

    $namaFile = $_FILES['avatar']['name'];
    $ukuranFiles = $_FILES['avatar']['size'];
    $error = $_FILES['avatar']['error'];
    $tmpName = $_FILES['avatar']['tmp_name'];

    // Cek apakah yang diupload adalah gambar
    $ekstensiAvatarValid = ['', 'jpg', 'jpeg', 'png'];
    $ekstensiAvatar = explode('.', $namaFile);
    $ekstensiAvatar = strtolower(end($ekstensiAvatar));
    if (!in_array($ekstensiAvatar, $ekstensiAvatarValid)) {
        // Jika Avatar Bukan Gambar
        return -1;
    }

    if ($ukuranFiles > 10000000) {
        // Cek jika ukuran terlalu besar
        return -2;
    }

    // Gambar Siap Upload
    // generate nama gambar baru

    $namaFileBaru = uniqid();
    $namaFileBaru .= '.';
    $namaFileBaru .= $ekstensiAvatar;

    move_uploaded_file($tmpName, '../../assets/dist/img/profile/' . $namaFileBaru);

    return $namaFileBaru;
}


function addSiswa($data)
{
    global $db;
    $user_id = (int) $_SESSION["id"];
    $id_siswa = (int)$data['id_siswa'];
    $nama_siswa = trim(mysqli_real_escape_string($db, $data["nama_siswa"]));
    $nis = trim(mysqli_real_escape_string($db, $data["nis"]));
    $kelas = trim(mysqli_real_escape_string($db, $data["kelas"]));
    $tanggal_lahir = trim(mysqli_real_escape_string($db, $data["tanggal_lahir"]));
    $jenis_kelamin = trim(mysqli_real_escape_string($db, $data["jenis_kelamin"]));
    $no_telfon = trim(mysqli_real_escape_string($db, $data["no_telfon"]));
    $email = trim(mysqli_real_escape_string($db, $data["email"]));
    $alamat = trim(mysqli_real_escape_string($db, $data["alamat"]));
    $created_at = date('Y-m-d H:i:s');

    $query = "SELECT * FROM siswa WHERE nis = '$nis' AND id_siswa != $id_siswa";
    $result = mysqli_query($db, $query);
    if (mysqli_fetch_assoc($result)) {
        return -1;
    }

    // Periksa apakah id_siswa sudah ada
    $query_id = "SELECT * FROM siswa WHERE id_siswa = $id_siswa";
    $result_id = mysqli_query($db, $query_id);

    if (mysqli_fetch_assoc($result_id)) {
        // id_product tidak ditemukan
        return -2;
    }

    $query = "INSERT INTO siswa VALUES 
    ('$id_siswa','$user_id', '$nis', '$nama_siswa', '$jenis_kelamin', '$tanggal_lahir', '$no_telfon', '$email', '$kelas', '$alamat', '$created_at')";
    mysqli_query($db, $query);

    return mysqli_affected_rows($db);
}

function editSiswa($data)
{
    global $db;
    $id_siswa = (int)$data['id_siswa'];
    $nama_siswa = trim(mysqli_real_escape_string($db, $data["nama_siswa"]));
    $nis = trim(mysqli_real_escape_string($db, $data["nis"]));
    $kelas = trim(mysqli_real_escape_string($db, $data["kelas"]));
    $tanggal_lahir = trim(mysqli_real_escape_string($db, $data["tanggal_lahir"]));
    $jenis_kelamin = trim(mysqli_real_escape_string($db, $data["jenis_kelamin"]));
    $no_telfon = trim(mysqli_real_escape_string($db, $data["no_telfon"]));
    $email = trim(mysqli_real_escape_string($db, $data["email"]));
    $alamat = trim(mysqli_real_escape_string($db, $data["alamat"]));
    // Periksa apakah nis siswa sudah ada, tetapi abaikan baris yang sedang diedit
    $query = "SELECT * FROM siswa WHERE nis = '$nis' AND id_siswa != $id_siswa";
    $result = mysqli_query($db, $query);

    if (mysqli_fetch_assoc($result)) {
        return -1;
    }

    // Jika nis siswa tidak ada yang duplikat, lakukan update
    $query = "UPDATE siswa SET nis = '$nis', 
                     nama_siswa = '$nama_siswa',
                     kelas = '$kelas',
                     tanggal_lahir = '$tanggal_lahir',
                     jenis_kelamin = '$jenis_kelamin',
                     no_telfon = '$no_telfon',
                     email = '$email',
                     alamat = '$alamat'
                     WHERE id_siswa = $id_siswa";
    mysqli_query($db, $query);

    return mysqli_affected_rows($db);
}

function deleteSiswa($id_siswa)
{
    global $db;
    mysqli_query($db, "DELETE FROM siswa WHERE id_siswa = $id_siswa");
    return mysqli_affected_rows($db);
}


function editNilai($data)
{
    global $db;

    $id_siswa   = (int)$data['id_siswa'];
    $nilai_uts  = isset($data['nilai_uts']) ? (int)$data['nilai_uts'] : 0;
    $nilai_uas  = isset($data['nilai_uas']) ? (int)$data['nilai_uas'] : 0;
    $keaktifan  = isset($data['keaktifan']) ? (int)$data['keaktifan'] : 0;
    $now        = date('Y-m-d H:i:s');

    // 🔥 MULAI TRANSACTION DI SINI
    mysqli_begin_transaction($db);

    try {

        // 🔍 CEK DATA
        $cek = mysqli_query($db, "
            SELECT id_penilaian 
            FROM penilaian 
            WHERE id_siswa = $id_siswa 
            LIMIT 1
        ");

        if (!$cek) {
            throw new Exception(mysqli_error($db));
        }

        if (mysqli_num_rows($cek) > 0) {

            // ✅ UPDATE
            $update = mysqli_query($db, "
                UPDATE penilaian SET
                    nilai_uts = $nilai_uts,
                    nilai_uas = $nilai_uas,
                    keaktifan = $keaktifan,
                    updated_at = '$now'
                WHERE id_siswa = $id_siswa
            ");

            if (!$update) {
                throw new Exception(mysqli_error($db));
            }
        } else {

            // ✅ INSERT
            $insert = mysqli_query($db, "
                INSERT INTO penilaian
                    (id_siswa, nilai_uts, nilai_uas, keaktifan, created_at)
                VALUES
                    ($id_siswa, $nilai_uts, $nilai_uas, $keaktifan, '$now')
            ");

            if (!$insert) {
                throw new Exception(mysqli_error($db));
            }
        }

        // 🔥 JIKA SEMUA BERHASIL
        mysqli_commit($db);
        return 1;
    } catch (Exception $e) {

        // 🔥 JIKA ADA ERROR
        mysqli_rollback($db);

        // optional debug
        // echo $e->getMessage();

        return 0;
    }
}

function deleteNilaiSiswa($id_siswa)
{
    global $db;
    mysqli_query($db, "DELETE FROM penilaian WHERE id_siswa = $id_siswa");
    return mysqli_affected_rows($db);
}


function deleteHasil($id_hasil)
{
    global $db;
    mysqli_query($db, "DELETE FROM hasil WHERE id_hasil = $id_hasil");
    return mysqli_affected_rows($db);
}

function addVariabel($data)
{
    global $db;
    $id_variabel = (int)$data['id_variabel'];
    $nama_variabel = trim(mysqli_real_escape_string($db, $data["nama_variabel"]));
    $created_at = date('Y-m-d H:i:s');

    $query = "SELECT * FROM variabel WHERE nama_variabel = '$nama_variabel' AND id_variabel != $id_variabel";
    $result = mysqli_query($db, $query);
    if (mysqli_fetch_assoc($result)) {
        return -1;
    }

    // Periksa apakah id)variabel sudah ada
    $query_id = "SELECT * FROM variabel WHERE id_variabel = $id_variabel";
    $result_id = mysqli_query($db, $query_id);

    if (mysqli_fetch_assoc($result_id)) {
        return -2;
    }

    $query = "INSERT INTO variabel VALUES 
    ('$id_variabel', '$nama_variabel', '$created_at', NULL)";
    mysqli_query($db, $query);

    return mysqli_affected_rows($db);
}

function editVariabel($data)
{
    global $db;
    $id_variabel = (int)$data['id_variabel'];
    $nama_variabel = trim(mysqli_real_escape_string($db, $data["nama_variabel"]));
    $updated_at   = date('Y-m-d H:i:s');
    // Periksa apakah nama variabel siswa sudah ada, tetapi abaikan baris yang sedang diedit
    $query = "SELECT * FROM variabel WHERE nama_variabel = '$nama_variabel' AND id_variabel != $id_variabel";
    $result = mysqli_query($db, $query);

    if (mysqli_fetch_assoc($result)) {
        return -1;
    }

    // Jika nis siswa tidak ada yang duplikat, lakukan update
    $query = "UPDATE variabel SET nama_variabel = '$nama_variabel',
                                  updated_at = '$updated_at'
                                WHERE id_variabel = $id_variabel";
    mysqli_query($db, $query);

    return mysqli_affected_rows($db);
}

function deleteVariabel($id_variabel)
{
    global $db;
    mysqli_query($db, "DELETE FROM variabel WHERE id_variabel = $id_variabel");
    return mysqli_affected_rows($db);
}

function tambahFuzzy($data)
{
    global $db;

    $id_variabel = trim(mysqli_real_escape_string($db, $data['id_variabel']));
    $nama_set    = trim(mysqli_real_escape_string($db, $data['nama_set']));
    $a           = trim(mysqli_real_escape_string($db, $data['a']));
    $b           = trim(mysqli_real_escape_string($db, $data['b']));
    $c           = trim(mysqli_real_escape_string($db, $data['c']));

    // ❌ Validasi kosong
    if ($id_variabel == "" || $nama_set == "" || $a == "" || $b == "" || $c == "") {
        return -3; // data kosong
    }

    // ❌ Validasi range
    if ($a > $b || $b > $c) {
        return -4; // format salah
    }

    // ❌ Cek duplikat (variabel + kategori)
    $cek = mysqli_query($db, "
        SELECT * FROM fuzzy_set 
        WHERE id_variabel = '$id_variabel' 
        AND nama_set = '$nama_set'
    ");

    if (mysqli_num_rows($cek) > 0) {
        return -1; // sudah ada
    }

    // ✅ Insert
    $query = "INSERT INTO fuzzy_set (id_variabel, nama_set, a, b, c)
              VALUES ('$id_variabel', '$nama_set', '$a', '$b', '$c')";

    mysqli_query($db, $query);

    return mysqli_affected_rows($db);
}

function updateFuzzy($data)
{
    global $db;

    $id_set      = trim(mysqli_real_escape_string($db, $data['id_set']));
    $id_variabel = trim(mysqli_real_escape_string($db, $data['id_variabel']));
    $nama_set    = trim(mysqli_real_escape_string($db, $data['nama_set']));
    $a           = trim(mysqli_real_escape_string($db, $data['a']));
    $b           = trim(mysqli_real_escape_string($db, $data['b']));
    $c           = trim(mysqli_real_escape_string($db, $data['c']));

    // ❌ Validasi kosong
    if ($id_set == "" || $id_variabel == "" || $nama_set == "" || $a == "" || $b == "" || $c == "") {
        return -3;
    }

    // ❌ Validasi format a,b,c
    if ($a > $b || $b > $c) {
        return -4;
    }

    // ❌ Cek duplikat (kecuali dirinya sendiri)
    $cek = mysqli_query($db, "
        SELECT * FROM fuzzy_set 
        WHERE id_variabel = '$id_variabel'
        AND nama_set = '$nama_set'
        AND id_set != '$id_set'
    ");

    if (mysqli_num_rows($cek) > 0) {
        return -1;
    }

    // ✅ Update
    $query = "UPDATE fuzzy_set SET
                id_variabel = '$id_variabel',
                nama_set = '$nama_set',
                a = '$a',
                b = '$b',
                c = '$c'
              WHERE id_set = '$id_set'
    ";

    mysqli_query($db, $query);

    return mysqli_affected_rows($db);
}

function deleteFuzzySet($id_set)
{
    global $db;
    mysqli_query($db, "DELETE FROM fuzzy_set WHERE id_set = $id_set");
    return mysqli_affected_rows($db);
}


function addRules($data)
{
    global $db;
    $uts = mysqli_real_escape_string($db, $data['uts']);
    $uas = mysqli_real_escape_string($db, $data['uas']);
    $keaktifan = mysqli_real_escape_string($db, $data['keaktifan']);
    $output = mysqli_real_escape_string($db, $data['output']);
    $keterangan = mysqli_real_escape_string($db, $data['keterangan']);

    // Cek apakah data yang sama sudah ada di database
    $checkQuery = "SELECT COUNT(*) as count FROM rule_fuzzy 
                   WHERE uts = '$uts' 
                   AND uas = '$uas' 
                   AND keaktifan = '$keaktifan' 
                   AND output = '$output'
                   AND keterangan = '$keterangan'";

    $result = mysqli_query($db, $checkQuery);
    $row = mysqli_fetch_assoc($result);

    // Jika data sudah ada, return 0 atau pesan error
    if ($row['count'] > 0) {
        return 0;
    } else {
        $query = "INSERT INTO rule_fuzzy (uts, uas, keaktifan, output, keterangan) 
                  VALUES ('$uts', 
                          '$uas', 
                          '$keaktifan', 
                          '$output', 
                          '$keterangan')";

        mysqli_query($db, $query);
        return mysqli_affected_rows($db);
    }
}

function editRule($data)
{
    global $db;
    $id_rule = $data["id_rule"];
    $uts = mysqli_real_escape_string($db, $data['uts']);
    $uas = mysqli_real_escape_string($db, $data['uas']);
    $keaktifan = mysqli_real_escape_string($db, $data['keaktifan']);
    $output = mysqli_real_escape_string($db, $data['output']);
    $keterangan = mysqli_real_escape_string($db, $data['keterangan']);

    // Cek apakah data yang sama sudah ada di database
    $checkQuery = "SELECT COUNT(*) as count FROM rule_fuzzy 
                   WHERE uts = '$uts' 
                   AND uas = '$uas' 
                   AND keaktifan = '$keaktifan' 
                   AND output = '$output'
                   AND keterangan = '$keterangan'";

    $result = mysqli_query($db, $checkQuery);
    $row = mysqli_fetch_assoc($result);

    // Jika data sudah ada, return 0 atau pesan error
    if ($row['count'] > 0) {
        return 0;
    } else {
        $query = "UPDATE rule_fuzzy SET uts = '$uts',
                                        uas = '$uas',
                                        keaktifan = '$keaktifan',
                                        output = '$output',
                                        keterangan = '$keterangan' WHERE id_rule = $id_rule ";

        mysqli_query($db, $query);
        return mysqli_affected_rows($db);
    }
}

function deleteRuleFuzzy($id_rule)
{
    global $db;
    mysqli_query($db, "DELETE FROM rule_fuzzy WHERE id_rule = $id_rule");
    return mysqli_affected_rows($db);
}



// =====================
// 🔥 FUNCTION SEGITIGA (DI LUAR)
// =====================

// =====================
// 🔥 FUNGSI SEGITIGA (AMAN)
// =====================
function segitiga($x, $a, $b, $c)
{
    // luar range
    if ($x < $a || $x > $c) return 0;

    // handle puncak
    if ($x == $b) return 1;

    // handle sisi naik
    if ($x > $a && $x < $b) {
        return ($b - $a) != 0 ? ($x - $a) / ($b - $a) : 0;
    }

    // handle sisi turun
    if ($x > $b && $x < $c) {
        return ($c - $b) != 0 ? ($c - $x) / ($c - $b) : 0;
    }

    return 0;
}


// =====================
// 🔥 FUNCTION UTAMA
// =====================
function hitungFuzzySugeno($db, $nilai_uts, $nilai_uas, $keaktifan, $id_siswa, $user_id)
{
    // =====================
    // 🔥 REPLACE MODE
    // =====================
    $cek = query("SELECT * FROM hasil WHERE id_siswa = $id_siswa");

    if (!empty($cek)) {
        $id_hasil_lama = $cek[0]['id_hasil'];

        mysqli_query($db, "DELETE FROM hasil_detail WHERE id_hasil = $id_hasil_lama");
        mysqli_query($db, "DELETE FROM hasil WHERE id_hasil = $id_hasil_lama");
    }

    // =====================
    // 1. AMBIL FUZZY SET
    // =====================
    $fuzzy = query("SELECT * FROM fuzzy_set");

    $sets = [];

    foreach ($fuzzy as $f) {
        $sets[$f['id_variabel']][$f['nama_set']] = $f;
    }

    // =====================
    // VALIDASI
    // =====================
    if (!isset($sets[1]) || !isset($sets[2]) || !isset($sets[3])) {
        return [
            'hasil' => 0,
            'keterangan' => 'Data tidak lengkap',
            'id_hasil' => 0
        ];
    }

    // =====================
    // 2. HITUNG μ
    // =====================
    $miu_uts = [];
    $miu_uas = [];
    $miu_keaktifan = [];

    foreach ($sets[1] as $nama => $s) {
        $miu_uts[$nama] = segitiga($nilai_uts, $s['a'], $s['b'], $s['c']);
    }

    foreach ($sets[2] as $nama => $s) {
        $miu_uas[$nama] = segitiga($nilai_uas, $s['a'], $s['b'], $s['c']);
    }

    foreach ($sets[3] as $nama => $s) {
        $miu_keaktifan[$nama] = segitiga($keaktifan, $s['a'], $s['b'], $s['c']);
    }

    // =====================
    // 3. AMBIL RULE
    // =====================
    $rules = query("SELECT * FROM rule_fuzzy");

    $total_alpha = 0;
    $total_alpha_z = 0;
    $detail = [];

    // =====================
    // 4. HITUNG α DAN z
    // =====================
    foreach ($rules as $r) {

        // normalisasi nama biar aman
        $uts = ucfirst(strtolower($r['uts']));
        $uas = ucfirst(strtolower($r['uas']));
        $aktif = ucfirst(strtolower($r['keaktifan']));

        $alpha = min(
            $miu_uts[$uts] ?? 0,
            $miu_uas[$uas] ?? 0,
            $miu_keaktifan[$aktif] ?? 0
        );

        if ($alpha > 0) {

            $z = (float)$r['output'];

            $total_alpha += $alpha;
            $total_alpha_z += ($alpha * $z);

            $detail[] = [
                'id_rule' => $r['id_rule'],
                'alpha' => $alpha,
                'z' => $z
            ];
        }
    }

    // =====================
    // 5. DEFUZZIFIKASI
    // =====================
    $hasil = ($total_alpha != 0) ? ($total_alpha_z / $total_alpha) : 0;

    $hasil = round($hasil, 2);

    // =====================
    // 🔥 6. KETERANGAN (FIX)
    // =====================
    if ($hasil >= 80) {
        $keterangan = 'Layak';
    } elseif ($hasil >= 70) {
        $keterangan = 'Dipertimbangkan';
    } else {
        $keterangan = 'Tidak Layak';
    }

    // =====================
    // 7. SIMPAN HASIL
    // =====================
    mysqli_query($db, "
        INSERT INTO hasil (id_siswa, user_id, nilai_fuzzy, keterangan)
        VALUES ('$id_siswa', '$user_id', '$hasil', '$keterangan')
    ");

    $id_hasil = mysqli_insert_id($db);

    // =====================
    // 8. SIMPAN DETAIL
    // =====================
    foreach ($detail as $d) {
        mysqli_query($db, "
            INSERT INTO hasil_detail (id_hasil, id_rule, alpha, z)
            VALUES ('$id_hasil', '{$d['id_rule']}', '{$d['alpha']}', '{$d['z']}')
        ");
    }

    // =====================
    // 9. RETURN
    // =====================
    return [
        'hasil' => $hasil,
        'keterangan' => $keterangan,
        'id_hasil' => $id_hasil
    ];
}

function is_user_active($id)
{
    global $db;

    // Cek status pengguna berdasarkan ID
    $result = mysqli_query($db, "SELECT status FROM users WHERE id = '$id'");
    $row = mysqli_fetch_assoc($result);

    // Jika data ditemukan
    if ($row) {
        if ($row['status'] === 'Active') {
            return true;
        }
    }

    // Jika tidak aktif atau tidak ditemukan
    return false;
}
function logout()
{
    // Hapus semua data sesi
    $_SESSION = array();

    // Hapus cookie sesi jika ada
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(
            session_name(),
            '',
            time() - 3600,
            $params["path"],
            $params["domain"],
            $params["secure"],
            $params["httponly"]
        );
    }

    // Hancurkan sesi
    session_destroy();

    // Alihkan ke halaman login
    header("Location: " . BASEURL . "login/"); // Sesuaikan dengan halaman login Anda
    exit;
}
