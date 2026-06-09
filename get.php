<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: text/html; charset=utf-8');

if (isset($_GET['kd_part'])) {
    $kd_part = strtoupper(trim($_GET['kd_part']));
    
    // Penyesuaian URL menggunakan parameter TblmstpartSearch[vidpart]
    $url = "https://h3.indako.id/site/cekhargapartindex?TblmstpartSearch%5Bvidpart%5D=" . urlencode($kd_part) . "&TblmstpartSearch%5Bvpartdesc%5D=&TblmstpartSearch%5Bmhetpart%5D=&TblmstpartSearch%5Bvstatus%5D=";

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/120.0.0.0');
    
    $response = curl_exec($ch);
    curl_close($ch);

    // Cek apakah hasil kosong atau tidak ditemukan
    if (stripos($response, 'No results found') !== false || empty($response)) {
        echo "NOT_FOUND";
        exit;
    }

    // Ambil baris TR yang mengandung kode part berdasarkan data-key (huruf kapital)
    if (preg_match('/<tr[^>]*data-key="' . preg_quote($kd_part, '/') . '".*?>(.*?)<\/tr>/si', $response, $matches)) {
        echo "<tr>" . $matches[1] . "</tr>";
    } 
    // Fallback jika data-key bernilai angka indeks (seperti "0") namun baris tersebut berisi kode part yang dicari
    elseif (preg_match('/<tr[^>]*data-key="\d+".*?>.*?(' . preg_quote($kd_part, '/') . ').*?<\/tr>/si', $response, $matches_fallback)) {
        // Jika cocok dengan fallback, ambil utuh seluruh tag <tr> tersebut
        if (preg_match('/(<tr[^>]*data-key="\d+".*?>.*?<\/tr>)/si', $response, $matches_tr)) {
            echo $matches_tr[1];
        } else {
            echo "NOT_FOUND";
        }
    }
    else {
        echo "NOT_FOUND";
    }
}
?>