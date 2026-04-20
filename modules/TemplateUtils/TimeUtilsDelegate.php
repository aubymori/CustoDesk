<?php
namespace CustoDesk\TemplateUtils;

use CustoDesk\Util\TimeUtils;

class TimeUtilsDelegate
{
    public function now(): int
    {
        return TimeUtils::now();
    }

    public function format(int $timestamp, string $format): string
    {
        return TimeUtils::format($timestamp, $format);
    }

    public function formatDate(int $timestamp)
    {
        return TimeUtils::formatDate($timestamp);
    }

    public function formatTime(int $timestamp)
    {
        return TimeUtils::formatTime($timestamp);
    }

    public function formatDateTime(int $timestamp)
    {
        return TimeUtils::formatDateTime($timestamp);
    }
}