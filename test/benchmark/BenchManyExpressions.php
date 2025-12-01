<?php

declare(strict_types=1);

namespace TimoLehnertz\test\benchmark;

use TimoLehnertz\formula\Formula;
use TimoLehnertz\formula\procedure\Scope;
use TimoLehnertz\formula\type\Value;
use PhpBench\Attributes as Bench;
use TimoLehnertz\formula\type\FloatType;
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
        $scope->definePHP(true, 'getMeasurement', $this->getMeasurement(...), null, new FloatType());
        $this->formula = new Formula('((((getMeasurement("S1", null, null, "P0D", TagBehavior.PASS_ON)+getMeasurement("S2", null, null, "P0D", TagBehavior.PASS_ON))/2)<0)?null:((getMeasurement("S1", null, null, "P0D", TagBehavior.PASS_ON)+getMeasurement("S2", null, null, "P0D", TagBehavior.PASS_ON))/2))', $scope);
    }

    /**
     * Simulates the formula overhead of calculating a sensor for every minute for 6 months.
     */
    #[Bench\BeforeMethods('setUp')]
    #[Bench\Revs(2)]
    public function benchPassthroughValue() {
        ini_set('memory_limit', '2000M');
        for ($i=0; $i < 60*24*30*6; $i++) {
            $this->formula->calculate();
        }
    }

    public function passthroughValue(Value $input): Value {
        return $input;
    }

    public function getMeasurement(string $sensorID, ?MeasurementInterpolation $interpolation = null, ?bool $extrapolation = null, \DateInterval|int $timeOffsetToPast = new \DateInterval('P0D'), TagBehavior $tagBehavior = TagBehavior::PASS_ON): FloatValue {
        return new FloatValue(1);
    }
}
