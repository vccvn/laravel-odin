<?php
namespace Odin\Engines;

use Odin\Repositories\Html\AreaRepository;
use Odin\Web\HtmlAreaList;
use Odin\Web\Options;
use Odin\Files\Filemanager;
use Odin\Helpers\Arr;

class ViewDataEngine
{
    static $shared = false;

    
    public static function share($name = null, $value=null)
    {
        if(static::$shared) return true;;
        $a = $name?(is_array($name)?$name:(is_string($name)?[$name=>$value]: [])):[];
        view()->share($a);

        static::$shared = true;

        return true;
    }
}
