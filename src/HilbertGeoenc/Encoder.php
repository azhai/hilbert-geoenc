<?php
namespace HilbertGeoenc;

/**
 * Geohash，使用Hilbert空间算法
 */
class Encoder
{
    const BITS_PER_CHAR = 2;
    //经纬度范围
    const LAT_MIN = -90.0;
    const LAT_MAX = 90.0;
    const LNG_MIN = -180.0;
    const LNG_MAX = 180.0;

    //误差表，单位：m
    public static $prec_errors = [
        20015087, //20015.087 km
        10007543, //10007.543 km
         5003772, // 5003.772 km
         2501886, // 2501.886 km
         1250943, // 1250.943 km
          625471, //  625.471 km
          312736, //  312.736 km
          156368, //  156.368 km
           78184, //   78.184 km
           39092, //   39.092 km
           19546, //   19.546 km
         9772.99, // 9772.992  m
         4886.50, // 4886.496  m
         2443.25, // 2443.248  m
         1221.62, // 1221.624  m
          610.81, //  610.812  m
          305.41, //  305.406  m
          152.70, //  152.703  m
           76.35, //   76.351  m
           38.18, //   38.176  m
           19.09, //   19.088  m
            9.54, //  954.394 cm
            4.77, //  477.197 cm
    ];

    public static function encode_int4($code)
    {
        $_BASE4 = '0123';
        $code_size = intval(log($code, 2)) + 2;
        $code_len = floor($code_size / 2);
        $res = array_fill(0, $code_len, '0');
        for ($i = $code_len - 1; $i >= 0; $i --) {
            $res[$i] = $_BASE4[$code & 0b11];
            $code = $code >> 2;
        }
        return implode($res);
    }

    public static function rotate($n, $x, $y, $rx, $ry)
    {
        if (0 === $ry) {
            if (1 === $rx) {
                $x = $n - 1 - $x;
                $y = $n - 1 - $y;
            }
            return [$y, $x];
        }
        return [$x, $y];
    }

    protected function _coord2int($lng, $lat, $dim)
    {
        assert($dim >= 1);
        $lat_y = ($lat + self::LAT_MAX) / 180.0 * $dim; //[0 ... dim)
        $lng_x = ($lng + self::LNG_MAX) / 360.0 * $dim; //[0 ... dim)
        return [
            min($dim - 1, floor($lng_x)),
            min($dim - 1, floor($lat_y)),
        ];
    }

    protected function _xy2hash($x, $y, $dim)
    {
        $d = 0;
        $lvl = $dim >> 1;
        while ($lvl > 0) {
            $rx = intval(($x & $lvl) > 0);
            $ry = intval(($y & $lvl) > 0);
            $d += $lvl * $lvl * ((3 * $rx) ^ $ry);
            @list($x, $y) = self::rotate($lvl, $x, $y, $rx, $ry);
            $lvl = $lvl >> 1;
        }
        return $d;
    }

    public function encode($lng, $lat, $prec = 22)
    {
        assert($lng >= self::LNG_MIN && $lng <= self::LNG_MAX);
        assert($lat >= self::LAT_MIN && $lat <= self::LAT_MAX);
        $dim = 1 << (($prec * self::BITS_PER_CHAR) >> 1);
        @list($x, $y) = $this->_coord2int($lng, $lat, $dim);
        $code = $this->_xy2hash($x, $y, $dim);
        $result = self::encode_int4($code);
        return sprintf('%0' . $prec . 's', $result);
    }

    public function get_prelen($distance = 5000)
    {
    }
}
