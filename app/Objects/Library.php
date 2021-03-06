<?php


namespace App\Objects;


use Illuminate\Support\Collection;

class Library
{
    /**
     * @var int
     */
    public $id;

    /**
     * @var int
     */
    public $totalBooksCnt;

    /**
     * @var int
     */
    public $signProcessDays;

    /**
     * @var int
     */
    public $shipPerDay;

    /**
     * @var Collection|Book[]
     */
    public $books;

    /**
     * Library constructor.
     *
     * @param int $id
     * @param $totalBooksCnt
     * @param $signProcessDays
     * @param $shipPerDay
     * @param array $books
     */
    public function __construct(int $id, $totalBooksCnt, $signProcessDays, $shipPerDay, array $books)
    {
        $this->id = $id;
        $this->totalBooksCnt = (int)$totalBooksCnt;
        $this->signProcessDays = (int)$signProcessDays;
        $this->shipPerDay = (int)$shipPerDay;
        $this->books = collect($books)->sortByDesc('score');
    }
}
