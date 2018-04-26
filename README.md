# HilbertGeoenc

Geohash，使用Hilbert空间算法，只实现4位编码和估算距离两个方法

参考Python库 [geohash-hilbert](https://github.com/tammoippen/geohash-hilbert)

## 基本用法

```php
<?php
use HilbertGeoenc\Encoder;

$e = new Encoder();
$lng = 113.95196; $lat = 22.541497;
echo $e->encode($lng, $lat); //2313000100002333212012
```
