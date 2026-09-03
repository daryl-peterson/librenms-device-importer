<?php

namespace DRP\DeviceImporter;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;

function checkRedis(): bool {
    try {
        $redis = Redis::connection();
        $redis->ping();
        return true;
    } catch (\Exception $e) {
        Log::error('Redis connection failed: ' . $e->getMessage());
        Log::error($e->getTraceAsString());
        return false;
    }
}
