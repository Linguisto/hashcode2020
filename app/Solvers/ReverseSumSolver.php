<?php

namespace App\Solvers;

use Illuminate\Support\Arr;

/**
 * Class ReverseSumSolver
 *
 * @package App\Solvers
 */
class ReverseSumSolver extends ProblemSolver
{
    /**
     * @inheritDoc
     */
    public function solutionResult(): array
    {
        $menu = Arr::first($this->dataSet);
        $slicesWanted = Arr::first($menu);

        $slicesByPizza = collect(Arr::last($this->dataSet));

        $result = collect();
        $slicesByPizza->reverse()->each(function ($slicesCnt, $pizzaID) use ($slicesWanted, $result) {
            if ($result->isEmpty()) {
                $result->put($pizzaID, $slicesCnt);

                return;
            }

            if ($result->sum() + $slicesCnt >= $slicesWanted) {
                return;
            }

            $result->put($pizzaID, $slicesCnt);
        });

        return [
            $result->count(),
            $result->reverse()->keys()->toArray(),
        ];
    }
}
