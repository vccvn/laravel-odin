<?php

namespace Odin\Crawlers;

use Odin\Repositories\Files\FileRepository;
use Odin\Repositories\Metadatas\MetadataRepository;

use Carbon\Carbon;
use Odin\Files\Image;
use Odin\Helpers\Arr;

class Crawler
{
    use Crawl;

    
     /**
     * chay lai thiet lap
     */
    public function __construct()
    {
        if(method_exists($this, 'init')){
            $this->init();
        }
    }

    public function __call($name, $arguments)
    {
        
    }
}