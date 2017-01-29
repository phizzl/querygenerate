<?php


namespace Phizzl\QueryGenerate\Drivers;


interface QueryEscapeInterface
{
    /**
     * @param string $string
     * @return string
     */
    public function escapeFieldName($string);

    /**
     * @param string $string
     * @return string
     */
    public function escapeValue($string);
}