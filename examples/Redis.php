<?php
require __DIR__ . "/../vendor/autoload.php";

use AAD\Cache\Cache;
use AAD\Cache\Drivers\Redis\Redis;

/**
 * Redis server a baglanti kuruyoruz.
 */
$connection = new \Redis();
$connection->connect('localhost', 6379);

/**
 * Redis storage uzerinden verilerin onbellege alinmasi icin Redis sinifini kullaniyoruz.
 */
$redis = Redis::init($connection);

/**
 * Yeni bir cache objesi olusturuyoruz.
 * Alternatif kullanimlar: Cache::init($redis) veya Cache::init(Redis::init($connection))
 */
$cache = new Cache($redis);

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
