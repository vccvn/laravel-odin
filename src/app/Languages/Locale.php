<?php

namespace Odin\Languages;

use Odin\Helpers\Arr;

class Locale
{
    /**
     * chứa tất cả cá loại data
     *
     * @var \Odin\Helpers\Arr
     */
    protected static $data = null;

    /**
     * kiểm tra xem biến data được set chưa
     * nếu chưa thì set mới
     *
     * @return void
     */
    public static function check()
    {
        if(!static::$data){
            static::$data = new Arr();
        }
    }
    /**
     * set data
     * @param string|array $key
     * @param mixed $value
     * 
     * @return boolean
     */
    public static function set($key, $value = null)
    {
        static::check();
        static::$data->set($key, $value);
        return true;
    }

    /**
     * lấy dữ liệu
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function get($key = null, $default = null)
    {
        static::check();
        return static::$data->get($key, $default);
    }

    public static function setDefault($value){
        return static::set('default', $value);
    }

    public static function setCurrent($value){
        return static::set('current', $value);
    }

    public static function default(){
        return static::get('default');
    }

    public static function current(){
        return static::get('current');
    }

    public static function isDefault(){
        static::check();
        return static::$data->get('current') == static::$data->get('default');
    }

    public static function all()
    {
        static::check();
        return static::$data->all();
    }
    
}
