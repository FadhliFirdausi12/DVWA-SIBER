
<?php

// Memeriksa apakah tombol Submit sudah ditekan
if( isset( $_POST[ 'Submit' ] ) ) {

    // Mengambil alamat IP yang dimasukkan pengguna
    $target = $_REQUEST[ 'ip' ];

    // Memvalidasi input agar hanya menerima alamat IPv4 yang valid
    // Berbeda dari blacklist, validasi ini hanya menerima format yang diizinkan
    if( filter_var( $target, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 ) ) {

        // Menentukan perintah ping berdasarkan sistem operasi
        if( stristr( php_uname( 's' ), 'Windows NT' ) ) {

            // Perintah ping untuk Windows
            $command = [ 'ping', '-n', '4', $target ];

        } else {

            // Perintah ping untuk Linux
            $command = [ 'ping', '-c', '4', $target ];
        }

        // Menentukan saluran input, output, dan error proses
        $descriptors = [
            0 => [ 'pipe', 'r' ],
            1 => [ 'pipe', 'w' ],
            2 => [ 'pipe', 'w' ]
        ];

        // Menjalankan ping menggunakan array agar input tidak diproses sebagai perintah shell
        $process = proc_open( $command, $descriptors, $pipes );

        // Memeriksa apakah proses berhasil dijalankan
        if( is_resource( $process ) ) {

            // Menutup saluran input yang tidak digunakan
            fclose( $pipes[0] );

            // Mengambil hasil output ping
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

    // Mengamankan output sebelum ditampilkan pada halaman HTML
    $html .= "<pre>" . htmlspecialchars( $cmd, ENT_QUOTES, 'UTF-8' ) . "</pre>";
}

?>
