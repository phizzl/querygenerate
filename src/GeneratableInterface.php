<?php

namespace Phizzl\QueryGenerate;


interface GeneratableInterface
{
    /**
     * Generate the query
     *
     * @return string
     */
    public function generate();
}