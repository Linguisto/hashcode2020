<?php


namespace App\Solvers;


use App\Objects\Book;
use App\Objects\Library;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

class WinnerSolver extends ProblemSolver
{
    /**
     * @var array
     */
    protected $result = [];

    /**
     * @inheritDoc
     */
    public function solutionResult(): array
    {
        $registry = [];
        $this->result = [
            $this->libraries->count(),
        ];

        foreach ($this->libraries as $library) {
            if (empty($registry)) {
                $registry += $library->books->pluck('id')->toArray();

                goto RESULT_PACKAGE;
            }

            $library->books = $library->books->reject(function (Book $book) use ($registry) {
                return in_array($book->id, $registry);
            });

            RESULT_PACKAGE:
            $this->result = array_merge($this->result, $this->packResult($library));
        }

        return $this->result;
    }

    /**
     * @param Library $library
     *
     * @return array
     */
    public function packResult(Library $library)
    {
        return [
            [$library->id, $library->books->count()],
            $library->books->pluck('id')->toArray(),
        ];
    }
}
