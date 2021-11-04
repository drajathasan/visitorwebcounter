# visitorwebcounter

Jika anda menggunakan SLiMS versi 9.4 maka copy paste script dibawah ini

\SLiMS\Plugins::getInstance()->execute('after_content_load');

pada file index.php dibawah script

// main content grab
$main_content = ob_get_clean();

Contoh :

