<?php

namespace App\Objects;
/**
 * Class Book
 *
 * @package App\Objects
 */
class Book
{
    public $id;
    public $score;

    /**
     * Book constructor.
     *
     * @param $id
     * @param $score
     */
    public function __construct($id, $score)
    {
        $this->id = $id;
        $this->score = $score;
    }
}
