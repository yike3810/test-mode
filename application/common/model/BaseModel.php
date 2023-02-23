<?php

namespace app\common\model;
\think\Loader::import('Suanfa.Sm4Helper');


class BaseModel extends \think\Model
{
    //加密密钥
    private $key = "JICFuN6leqblHOxDyiFR5yMgJWxzt6";

    /**
     * SM4 加密
     * @param string $value
     * @return mixed|string
     */
    public function sm4_encrypt($value = "")
    {
        $sm4 = new \Sm4Helper();
        if ($value != '') {
            if ($sm4->decrypt($this->key, $value)) {
                return $value;
            } else {
                return $sm4->encrypt($this->key, $value);
            }
        } else {
            return "";
        }
    }

    /**
     * SM4 解密
     * @param string $value
     * @return bool|mixed|string
     */
    public function sm4_decrypt($value = "")
    {
        $sm4 = new \Sm4Helper();
        if ($value != '') {
            if ($value_dec = $sm4->decrypt($this->key, $value)) {
                return $value_dec;
            } else {
                return $value;
            }
        } else {
            return "";
        }
    }
}