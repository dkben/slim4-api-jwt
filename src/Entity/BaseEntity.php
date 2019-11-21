<?php


namespace App\Entity;


use App\Exception\EntityValidateException;
use App\Exception\ExceptionResponse;


class BaseEntity
{
    /**
     * @param array $array
     * @return bool
     */
    public function setFromArray(Array $array)
    {
        if (is_array($array) && !is_null($array)) {
            foreach ($array AS $key => $value) $this->{$key} = $value;

            return true;
        } else {
            return false;
        }
    }

    //Mapping a JSON object to Data Object
    public function set($json)
    {
        if (is_array($json)) {
            return $this->setFromArray($json);
        } else {
            if (is_object($json)) {
                $json = json_encode($json, 522);
            }
            $data = json_decode($json, true);
            if (is_array($data) && !is_null($data)) {
                foreach ($data AS $key => $value)
                    $this->{$key} = is_array($value) ? (object)$value : $value;

                return true;
            } else {
                return false;
            }
        }
    }

    /**
     * 是否需要拋出異常
     * @param $result boolean
     */
    protected function entityValidate($result)
    {
        try {
            if ($result) {
                throw new EntityValidateException();
            }
        } catch (EntityValidateException $e) {
            ExceptionResponse::response($e->getMessage(), $e->getCode());
        }
    }

}