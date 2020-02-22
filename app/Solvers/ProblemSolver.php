<?php

namespace App\Solvers;

use App\Objects\Book;
use App\Objects\Library;
use App\Solvers\Contracts\ProvidesSolution;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

/**
 * Class DataParser
 *
 * @package App\Services
 */
abstract class ProblemSolver implements ProvidesSolution
{
    /**
     * @var Collection
     */
    protected $dataSet;

    /**
     * @var Collection|Library[]
     */
    protected $libraries;

    /**
     * @var int
     */
    protected $overAllDays;

    /**
     * ProblemSolver constructor.
     *
     * @param array $dataSet
     */
    public function __construct(array $dataSet)
    {
        $this->dataSet = collect($dataSet);
    }
}
