<?php

namespace App\Solvers;

use Illuminate\Support\Arr;

/**
 * Class WinnerSolver
 *
 * @package App\Solvers
 */
class PRStupidSolver extends ProblemSolver
{
    /**
     * @inheritDoc
     */
    public function solutionResult(): array
    {
        $menu = Arr::first($this->dataSet);
        $slicesWanted = Arr::first($menu);

        $slicesByPizza = Arr::last($this->dataSet);

        $result = collect();
        foreach ($slicesByPizza as $pizzaID => $slicesCnt) {
            if (empty($result)) {
                $result->put($pizzaID, $slicesCnt);
                continue;
            }

            if ($result->sum() + $slicesCnt >= $slicesWanted) {
                break;
            }

            $result->put($pizzaID, $slicesCnt);
        }

        return [
            $result->count(),
            $result->keys()->toArray(),
        ];
    }
}
