<?php
require __DIR__ . "/vendor/autoload.php";

use AAD\Cache\Cache;
use AAD\Cache\File;

/**
 * Konfigurasyon bilgilerinin tanimlanmasi;
 * cache_dir: Onbellege alinacak verilerin depolanacagi dizin.
 * cache_ttl: Verilerin onbellekte tutulacagi sure. Bu bilgi verinin on bellekte tutulmasi icin islem yapildiginda ozellestirilebilir. -1 sonsuza kadar tutulacagi anlamina gelir.
 */
$config = [
    'cache_dir' => __DIR__ . '/cache',
    'cache_ttl' => 180,
];

/**
 * Dosya sistemi uzerinden verilerin onbellege alinmasi icin File sinifini kullaniyoruz.
 */
$file = File::init($config);

/**
 * Yeni bir cache objesi olusturuyoruz.
 * Alternatif kullanimlar: Cache::init($file) veya Cache::init(File::init($config))
 */
$cache = new Cache($file);

/**
 * Verinin daha onceden onbellege alinip/alinmadigini kontrol ediyoruz.
 * $cache->get('ornek') isteginin yaniti false dondugunde verinin daha once kayit edilmedigini anlayabiliriz.
 *
 * Sayet veri onbellege alinmamis ise okuduktan sonra 180 sn sure ile on bellekte tutulmasi icin islem yapiyoruz.
 * Ayni kod 180 sn icerisinde tekrar calisirsa veriyi onbellekte yer alan bilgi ekrana yazilacak.
 *
 * Onbellekten veri silmek icin $cache->del('ornek') kullanilabilir.
 * Tum onbellegi temizlemek icin: $cache->clear() ya da $cache->flushall()
 */
$data = $cache->get('ornek');
if ($data === false) {
    /**
     * Veri onbellekte olmadigi durumda ekranda bu metin yazacak.
     */
    echo "Islem yeniden hesaplandi, on bellege yaziliyor...\n";

    /**
     * Kucuk bir matematiksek hesap yapalim.
     */
    $result = 1024 * 1024;
    $data = "1024 * 1024 = {$result}";

    /**
     * Ciktiyi/hesapladigimiz bilgiyi onbellege yaziyoruz.
     */
    $cache->set('ornek', $data, 180);
}

echo "Sonuc: {$data}";

/**
 * Verinin onbellekte olmadigi durumda ciktimiz:
 *   Islem yeniden hesaplandi, on bellege yaziliyor...
 *   Sonuc: 1024 * 1024 = 1048576
 *
 * Verinin onbellekte oldugu durumda ciktimiz:
 *    Sonuc: 1024 * 1024 = 1048576
 */
