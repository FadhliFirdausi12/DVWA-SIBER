
<?php

// Memeriksa apakah tombol Submit sudah ditekan
if( isset( $_POST[ 'Submit' ] ) ) {

    // Mengambil alamat IP yang dimasukkan pengguna
    $target = $_REQUEST[ 'ip' ];

    // Memvalidasi apakah input merupakan alamat IPv4 yang valid
    // FILTER_FLAG_IPV4 memastikan hanya format IPv4 yang diterima
    if( filter_var( $target, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 ) ) {

        // Menentukan perintah ping berdasarkan sistem operasi
        if( stristr( php_uname( 's' ), 'Windows NT' ) ) {

            // Perintah ping untuk Windows
            $command = [ 'ping', '-n', '4', $target ];

        } else {

            // Perintah ping untuk Linux
            $command = [ 'ping', '-c', '4', $target ];
        }

        // Menentukan saluran komunikasi proses
        // 0 = input, 1 = output, 2 = error
        $descriptors = [
            0 => [ 'pipe', 'r' ],
            1 => [ 'pipe', 'w' ],
            2 => [ 'pipe', 'w' ]
        ];

        // Menjalankan perintah tanpa menggabungkan input ke string shell
        // Format array memisahkan perintah dan argumennya
        $process = proc_open( $command, $descriptors, $pipes );

        // Memastikan proses berhasil dijalankan
        if( is_resource( $process ) ) {

            // Menutup saluran input karena tidak digunakan
            fclose( $pipes[0] );

            // Membaca hasil output perintah ping
            $cmd = stream_get_contents( $pipes[1] );

            // Menutup saluran output dan error
            fclose( $pipes[1] );
            fclose( $pipes[2] );

            // Mengakhiri proses ping
            proc_close( $process );

        } else {

            // Pesan jika proses gagal dijalankan
            $cmd = "Unable to execute ping.";
        }

    } else {

        // Menolak input yang bukan alamat IPv4 valid
        $cmd = "ERROR: Invalid IPv4 address.";
    }

    // Mengamankan output agar tidak ditafsirkan sebagai HTML atau JavaScript
    $html .= "<pre>" . htmlspecialchars( $cmd, ENT_QUOTES, 'UTF-8' ) . "</pre>";
}

?>
