<?php

namespace App\Solvers;

use App\Objects\Book;
use App\Objects\Library;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

/**
 * Class WinnerSolver
 *
 * @package App\Solvers
 */
class WinnerSolver extends ProblemSolver
{
    /**
     * @var array
     */
    protected $result = [];

    /**
     * @var float
     */
    protected $scoreImportance;

    /**
     * @var float
     */
    protected $signProcessImportance;

    /**
     * WinnerSolver constructor.
     *
     * @param array $dataSet
     */
    public function __construct(array $dataSet)
    {
        parent::__construct($dataSet);

        $input = $this->dataSet->get(0);
        $booksData = $this->dataSet->get(1);
        $librariesData = $this->dataSet->slice(2)->chunk(2);

        $books = collect();
        foreach ($booksData as $id => $score) {
            $books->put($id, new Book($id, $score));
        }

        [$this->scoreImportance, $this->signProcessImportance] = $this->datasetRates($books);

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


    /**
     * @inheritDoc
     */
    public function solutionResult(): array
    {
        $registry = [];

        $this->libraries = $this->libraries->sort(function (Library $current, Library $next) {
            return $this->usefulnessIndex($next) <=> $this->usefulnessIndex($current);
        });

        $resultLibsCount = 0;
        foreach ($this->libraries as $library) {
            if (empty($registry)) {
                $registry += $library->books->pluck('id')->toArray();

                goto RESULT_PACKAGE;
            }

            $library->books = $library->books->reject(function (Book $book) use ($registry) {
                return in_array($book->id, $registry);
            });

            if ($library->books->isEmpty()) {
                continue;
            }

            RESULT_PACKAGE:
            ++$resultLibsCount;

            $this->result = array_merge($this->result, $this->packResult($library));
        }

        array_unshift($this->result, $resultLibsCount);

        return $this->result;
    }

    /**
     * @param Library $library
     *
     * @return float
     */
    protected function usefulnessIndex(Library $library): float
    {
        // code that worked well
        /*$effectiveBooksCount = $library->shipPerDay * ($this->overAllDays / $library->signProcessDays);
        return $library
                ->books
                ->slice(0, $effectiveBooksCount)
                ->sum('score') / $library->signProcessDays;*/

        // code that has broken all
        $effectiveDaysCount = $this->overAllDays - $library->signProcessDays;
        $effectiveBooksCount = $library->shipPerDay * $effectiveDaysCount;

        $libScore = $library->books->sum('score');
        $effectiveLibScore = $library->books->slice(0, $effectiveBooksCount)->sum('score');

        $libScoreSaldo = $libScore / $effectiveLibScore;
        $libScore *= $libScoreSaldo;

        $scoreRate = $libScore / $effectiveDaysCount;
        $signProcessRate = $this->overAllDays / $library->signProcessDays;

        return $scoreRate * $this->scoreImportance + $signProcessRate * $this->signProcessImportance;
    }

    /**
     * @param Collection $books
     *
     * @return array
     */
    protected function datasetRates(Collection $books): array
    {
        $maxBookScore = $books->max('score');
        $averageScore = $books->avg('score');
        $scoreImportanceCff = $maxBookScore - $averageScore;

        while ($scoreImportanceCff > 1) {
            $scoreImportanceCff /= 10;
        }

        $scoreImportance = 1 + $scoreImportanceCff;
        $signProcessImportance = 1 - $scoreImportanceCff;

        return [
            $scoreImportance,
            $signProcessImportance,
        ];
    }

    /**
     * @param Library $library
     *
     * @return array
     */
    protected function packResult(Library $library)
    {
        return [
            [$library->id, $library->books->count()],
            $library->books->pluck('id')->toArray(),
        ];
    }
}
