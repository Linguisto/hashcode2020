<?php


namespace App\Solvers;


use App\Objects\Book;
use App\Objects\Library;

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
     * @inheritDoc
     */
    public function solutionResult(): array
    {
        $registry = [];

        $this->libraries = $this->libraries->sort(function (Library $current, Library $next) {
            return $next->usefulnessIndex() <=> $current->usefulnessIndex();
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
