<?php

namespace App\Contracts\Stats;

interface StatisticsProvider
{
    public function getStats(): array;
}