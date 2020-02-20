<?php


namespace App\Solvers;


use App\Objects\Book;
use App\Objects\Library;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

class WinnerSolver extends ProblemSolver
{

    /**
     * @inheritDoc
     */
    public function solutionResult(): array
    {
        $registry = [];

        $libraries = $this->libraries->map(function (Library $library) use (&$registry) {
            if (empty($registry)) {
                $registry += $library->books->pluck('id')->toArray();

                return $library;
            }

            $rejected = collect();
            $library->books = $library->books->reject(function (Book $book) use ($registry, $rejected) {
                if (in_array($book->id, $registry)) {
                    $rejected->push($book);

                    return true;
                }

                return false;
            });

            $rejected = $rejected->sortBy('score');
            while (
                ! $rejected->isEmpty() &&
                (($library->books->count() / $library->shipPerDay) / $this->overAllDays < 1)
            ) {
                $library->books->push($rejected->shift());
            }

            return $library;
        });

        return $this->packResult($libraries);
    }

    /**
     * @param Collection $libraries
     *
     * @return array
     */
    public function packResult(Collection $libraries)
    {
        $result = [
            $libraries->count(),
        ];

        foreach ($libraries as $library) {
            $result[] = [$library->id, $library->books->count()];
            $result[] = $library->books->pluck('id')->toArray();
        }

        return $result;
    }
}
