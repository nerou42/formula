<?php

declare(strict_types=1);

namespace TimoLehnertz\test\benchmark;

use TimoLehnertz\formula\Formula;
use TimoLehnertz\formula\procedure\Scope;
use TimoLehnertz\formula\type\Value;
use PhpBench\Attributes as Bench;
use TimoLehnertz\formula\type\FloatValue;

enum MeasurementInterpolation: string {
    case OFF = 'O';
    case FILL = 'F';
    case LINEAR = 'L';
}

enum TagBehavior {
  case PASS_ON;
  case SKIP;
}

class BenchManyExpressions {

    private Formula $formula;


    public function setUp(): void {
        $scope = new Scope();
        $scope->definePHP(true, 'MeasurementInterpolation', MeasurementInterpolation::class);
        $scope->definePHP(true, 'TagBehavior', TagBehavior::class);
        $scope->definePHP(true, 'passthroughValue', $this->passthroughValue(...));
        $scope->definePHP(true, 'getMeasurement', $this->getMeasurement(...));
        $this->formula = new Formula('((((getMeasurement("S1", null, null, "P0D", TagBehavior.PASS_ON)+getMeasurement("S2", null, null, "P0D", TagBehavior.PASS_ON))/2)<0)?null:((getMeasurement("S1", null, null, "P0D", TagBehavior.PASS_ON)+getMeasurement("S2", null, null, "P0D", TagBehavior.PASS_ON))/2))', $scope);
    }

    #[Bench\BeforeMethods('setUp')]
    #[Bench\Revs(10)]
    public function benchPassthroughValue() {
        // ini_set('memory_limit', '8M');
        for ($i=0; $i < 1000; $i++) { 
            $this->formula->calculate();
        }
    }

    // #[Bench\Revs(1000)]
    // public function benchCallUserFunc() {
        
    //     // $func = $this->getMeasurement(...);
    //     // for ($i=0; $i < 100000; $i++) { 
    //     //     call_user_func_array($func, ["S1", MeasurementInterpolation::FILL, false, new \DateInterval('P1D')]);
    //     // }
    // }

    public function passthroughValue(Value $input): Value {
        return $input;
    }

    public function getMeasurement(string $sensorID, ?MeasurementInterpolation $interpolation = null, ?bool $extrapolation = null, \DateInterval|int $timeOffsetToPast = new \DateInterval('P0D'), TagBehavior $tagBehavior = TagBehavior::PASS_ON): float {
        return 1;
    }
}
