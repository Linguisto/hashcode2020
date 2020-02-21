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

        $input = $this->dataSet->get(0);
        $booksData = $this->dataSet->get(1);
        $librariesData = $this->dataSet->slice(2)->chunk(2);

        $books = collect();
        foreach ($booksData as $id => $score) {
            $books->put($id, new Book($id, $score));
        }

        $libraries = collect();

        /**
         * @var int $id
         * @var Collection $libraryDatum
         */
        foreach ($librariesData as $id => $libraryDatum) {
            $libraryBooks = $books->only($libraryDatum->last())->toArray();
            $params = collect($libraryDatum->first())->prepend($id)->push($libraryBooks);
            $libraries->push(new Library(...$params));
        }

        $this->libraries = $libraries->sortByDesc('signProcessDays');

        $this->overAllDays = (int)Arr::last($input);
    }
}
