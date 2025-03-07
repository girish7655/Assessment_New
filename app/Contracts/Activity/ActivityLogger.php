<?php

namespace App\Contracts\Activity;

interface ActivityLogger
{
    public function getRecentActivities(int $limit = 5): array;
}