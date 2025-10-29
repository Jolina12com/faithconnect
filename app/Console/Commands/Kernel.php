protected function schedule(Schedule $schedule)
{
    // Clean up stale streams every hour
    $schedule->call(function () {
        app(LiveStreamController::class)->cleanupStaleStreams();
    })->hourly();
}