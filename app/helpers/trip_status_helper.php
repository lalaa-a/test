<?php

if (!function_exists('shouldMoveTripToOngoingToday')) {
    function shouldMoveTripToOngoingToday($status, $startDate, $endDate, $today = null)
    {
        $normalizedStatus = strtolower(trim((string)$status));
        if ($normalizedStatus !== 'scheduled') {
            return false;
        }

        if (empty($startDate) || empty($endDate)) {
            return false;
        }

        try {
            $start = (new DateTimeImmutable((string)$startDate))->setTime(0, 0, 0);
            $end = (new DateTimeImmutable((string)$endDate))->setTime(0, 0, 0);

            if ($end < $start) {
                return false;
            }

            if ($today instanceof DateTimeInterface) {
                $todayDate = DateTimeImmutable::createFromInterface($today)->setTime(0, 0, 0);
            } elseif (!empty($today)) {
                $todayDate = (new DateTimeImmutable((string)$today))->setTime(0, 0, 0);
            } else {
                $todayDate = (new DateTimeImmutable('today'))->setTime(0, 0, 0);
            }
        } catch (Exception $e) {
            return false;
        }

        return $todayDate >= $start && $todayDate <= $end;
    }
}
