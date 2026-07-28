<?php
namespace App\Console\Commands;

use App\Models\AdminUser;
use App\Models\Device;
use App\Models\Notification;
use Illuminate\Console\Command;
use Carbon\Carbon;

class CheckDeviceHealth extends Command
{
    protected $signature = 'devices:check-health';
    protected $description = 'Check all active devices for offline status and notify ICTMO admins if any have gone silent.';

    private const OFFLINE_THRESHOLD_MINUTES = 10;
    private const RE_ALERT_COOLDOWN_MINUTES = 60;

    public function handle(): int
    {
        $devices = Device::where('is_active', true)->get();
        $alertedCount = 0;

        foreach ($devices as $device) {
            if (! $device->last_seen_at) {
                continue;
            }

            $minutesSinceLastSeen = Carbon::parse($device->last_seen_at)->diffInMinutes(now());

            if ($minutesSinceLastSeen < self::OFFLINE_THRESHOLD_MINUTES) {
                continue;
            }

            if ($device->last_alerted_at) {
                $minutesSinceLastAlert = Carbon::parse($device->last_alerted_at)->diffInMinutes(now());
                if ($minutesSinceLastAlert < self::RE_ALERT_COOLDOWN_MINUTES) {
                    continue;
                }
            }

            $this->notifyIctmoAdmins($device, $minutesSinceLastSeen);

            $device->update(['last_alerted_at' => now()]);
            $alertedCount++;
        }

        $this->info("Device health check complete. {$alertedCount} offline alert(s) sent.");

        return self::SUCCESS;
    }

    private function notifyIctmoAdmins(Device $device, int $minutesOffline): void
    {
        $admins = AdminUser::where('role', 'ictmo')->where('is_active', true)->get();

        $hours = intdiv($minutesOffline, 60);
        $mins  = $minutesOffline % 60;
        $duration = $hours > 0 ? "{$hours}h {$mins}m" : "{$mins}m";

        $message = "Device \"{$device->device_name}\" ({$device->location}) has been offline for {$duration}.";

        foreach ($admins as $admin) {
            Notification::create([
                'admin_id'   => $admin->admin_id,
                'notif_type' => 'device_offline',
                'message'    => $message,
                'is_read'    => false,
            ]);
        }
    }
}