<?php

namespace Odin\Events;

use ArrayAccess;
use ArrayIterator;
use Countable;
use IteratorAggregate;
use JsonSerializable;

class EventDispatch implements Countable, ArrayAccess, IteratorAggregate, JsonSerializable
{
    protected $__data__ = [];
    protected $__status__ = true;
    /**
     * khoi tao doi tuong
     * @param array|object $data
     */
    function __construct($data = [])
    {
        if (is_array($data) || is_object($data)) {
            foreach ($data as $key => $value) {
                // duyệt qua mảng hoặc object để gán key, value ở level 0 cho biến data
                $this->__data__[$key] = $value;
            }
        }
    }

    public function preventDefault()
    {
        $this->__status__ = false;
    }

    public function getStatus()
    {
        return $this->__status__;
    }

    /**
     * đếm phần tử
     * @return int
     */
    public function count(): int
    {
        return count($this->__data__);
    }


    /**
     * kiểm tra tồn tại
     *
     * @return boolean
     */
    public function  __isset($key)
    {
        return isset($this->__data__[$key]);
    }

    /**
     * xóa phần tử
     * @param string $key
     */
    public function __unset($key)
    {
        unset($this->__data__[$key]);
    }

    public function offsetSet($offset, $value): void
    {
        if (is_null($offset)) {
            $this->__data__[] = $value;
        } else {
            $this->__data__[$offset] = $value;
        }
    }

    public function offsetExists($offset): bool
    {
        return isset($this->__data__[$offset]);
    }

    public function offsetUnset($offset): void
    {
        unset($this->__data__[$offset]);
    }

    public function offsetGet($offset): mixed
    {
        return isset($this->__data__[$offset]) ? $this->__data__[$offset] : null;
    }


    public function __set($offset, $value): void
    {
        $this->offsetSet($offset, $value);
    }

    public function __get($offset)
    {
        return isset($this->__data__[$offset]) ? $this->__data__[$offset] : null;
    }

    /**
     * Get an iterator for the items.
     *
     * @return ArrayIterator
     */
    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->__data__);
    }



    public function toArray()
    {
        return $this->__data__;
    }



    public function toJson($options = 0)
    {
        return json_encode($this->toArray());
    }


    /**
     * Convert the object into something JSON serializable.
     *
     * @return array
     */
    public function jsonSerialize():array
    {
        return array_map(function ($value) {
            if ($value instanceof JsonSerializable) {
                return $value->jsonSerialize();
            } elseif ($value instanceof static) {
                return $value->toArray();
            }

            return $value;
        }, $this->toArray());
    }


    /**
     * gọi hàm với tên thuộc tính với tham số là giá trị default
     * @param string $name
     * @param array $arguments
     */
    public function __call($name, $arguments)
    {

        return isset($this->__data__[$name]) ? $this->__data__[$name] : (array_key_exists('0', $arguments) ? $arguments[0] : null);
    }
}
