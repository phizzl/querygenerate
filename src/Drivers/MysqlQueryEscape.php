<?php


namespace Phizzl\QueryGenerate\Drivers;


use Phizzl\QueryGenerate\GeneratorException;

class MysqlQueryEscape implements QueryEscapeInterface
{
    /**
     * @inheritdoc
     */
    public function escapeFieldName($string){
        if(is_array($string)){
            foreach($string as &$value){
                $value = "`{$value}`";
            }
            return implode(', ', $string);
        }
        return "`{$string}`";
    }

    /**
     * @inheritdoc
     * @throws GeneratorException
     */
    public function escapeValue($mixed){
        if(is_numeric($mixed)){
            return $this->escapeValueNumber($mixed);
        }
        elseif(is_bool($mixed)){
            return $this->escapeValueBool($mixed);
        }
        elseif(is_string($mixed)){
            return $this->escapeValueString($mixed);
        }
        elseif(is_array($mixed)){
            return $this->escapeValueArray($mixed);
        }

        throw new GeneratorException("Cannot escape content \n" . print_r($mixed, true));
    }

    /**
     * Escapes all values in a given array
     *
     * @param array $array
     * @return string
     */
    private function escapeValueArray(array $array){
        foreach($array as &$value){
            $value = $this->escapeValue($value);
        }

        return "(" . implode(', ', $array) . ")";
    }

    /**
     * @param $string
     * @return string
     */
    private function escapeValueString($string){
        return mysql_escape_string($string);
    }

    /**
     * @param bool $bool
     * @return int
     */
    private function escapeValueBool($bool){
        return $bool ? 1 : 0;
    }

    /**
     * @param $numeric
     * @return float|int
     */
    private function escapeValueNumber($numeric){
        if(is_float($numeric)){
            return floatval($numeric);
        }
        elseif(is_double($numeric)){
            return doubleval($numeric);
        }

        return intval($numeric);
    }
}