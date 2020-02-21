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
        $this->totalBooksCnt = $totalBooksCnt;
        $this->signProcessDays = $signProcessDays;
        $this->shipPerDay = $shipPerDay;
        $this->books = collect($books);
    }

    /**
     * @return float
     */
    public function usefulnessIndex(): float
    {
        return $this->books->sum('score') / $this->signProcessDays / $this->shipPerDay;
    }
}
