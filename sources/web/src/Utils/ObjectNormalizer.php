<?php
/**
 * Created by PhpStorm.
 * User: giscard
 * Date: 18/03/19
 * Time: 12:16
 */

namespace App\Utils;

/**
 * Interface ObjectNormalizer
 * @package App\Utils
 * Comments : Used to avoid serialization issues with ORM and "Symfony\Component\Serializer\SerializerInterface;" in doctrine - symfony4
 */
interface ObjectNormalizer
{
    /**
     * @param mixed[] $data
     * @param string $format
     * @return mixed
     */
    public function normalize(array $data, string $format = 'json');
}
