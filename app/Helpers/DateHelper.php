<?php

use Carbon\Carbon;

/**
 * Format a date-only value (no time, no timezone conversion needed).
 * Returns e.g. "27 May 2026" or "—" for null.
 */
function fmt_date(mixed $date, string $format = 'd M Y'): string
{
    if (!$date) return '—';
    return Carbon::parse($date)->format($format);
}

/**
 * Format a datetime value converted to Dubai/GST time (UTC+4).
 * Returns e.g. "27 May 2026, 17:31 GST" or "—" for null.
 */
function fmt_datetime(mixed $date, string $format = 'd M Y, H:i'): string
{
    if (!$date) return '—';
    return Carbon::parse($date)->setTimezone('Asia/Dubai')->format($format) . ' GST';
}

/**
 * Format a datetime for audit logs — includes seconds.
 * Returns e.g. "27 May 2026, 17:31:42 GST" or "—" for null.
 */
function fmt_datetime_audit(mixed $date): string
{
    if (!$date) return '—';
    return Carbon::parse($date)->setTimezone('Asia/Dubai')->format('d M Y, H:i:s') . ' GST';
}
