<?php

declare(strict_types=1);

namespace TimoLehnertz\test\benchmark;

use TimoLehnertz\formula\Formula;
use TimoLehnertz\formula\procedure\Scope;
use TimoLehnertz\formula\type\Value;
use PhpBench\Attributes as Bench;

class BenchPassthroughFunction {

    private Formula $formula;

    public function setUpValue(): void {
        $scope = new Scope();
        $scope->definePHP(true, 'passthroughValue', $this->passthroughValue(...));
        $this->formula = new Formula('passthroughValue("value")', $scope);
    }

    public function setUpMixed(): void {
        $scope = new Scope();
        $scope->definePHP(true, 'passthroughMixed', $this->passthroughMixed(...));
        $this->formula = new Formula('passthroughMixed("value")', $scope);
    }

    #[Bench\BeforeMethods('setUpValue')]
    #[Bench\Revs(1000)]
    public function benchPassthroughValue() {
        $this->formula->calculate();
    }

    #[Bench\BeforeMethods('setUpMixed')]
    #[Bench\Revs(1000)]
    public function benchPassthroughMixed() {
        $this->formula->calculate();
    }

    public function passthroughValue(Value $input): Value {
        return $input;
    }

    public function passthroughMixed(mixed $input): mixed {
        return $input;
    }
}
