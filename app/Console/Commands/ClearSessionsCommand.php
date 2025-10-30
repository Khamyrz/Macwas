<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class ClearSessionsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sessions:clear-all';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear all user sessions (force logout all users)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Clearing all user sessions...');
        
        $clearedCount = 0;
        
        // Clear file-based sessions
        $clearedCount += $this->clearFileSessions();
        
        // Clear database sessions
        $clearedCount += $this->clearDatabaseSessions();
        
        // Clear cache-based sessions
        $this->clearCacheSessions();
        
        // Create flag file for middleware to detect
        $this->createForceLogoutFlag();
        
        $this->info("Successfully cleared all sessions. All users have been logged out.");
        
        return Command::SUCCESS;
    }

    /**
     * Clear file-based sessions
     */
    private function clearFileSessions(): int
    {
        $sessionPath = storage_path('framework/sessions');
        $clearedCount = 0;
        
        if (File::exists($sessionPath)) {
            $files = File::files($sessionPath);
            foreach ($files as $file) {
                if ($file->getExtension() === 'php') {
                    File::delete($file->getPathname());
                    $clearedCount++;
                }
            }
        }
        
        if ($clearedCount > 0) {
            $this->line("Cleared {$clearedCount} file-based sessions.");
        }
        
        return $clearedCount;
    }

    /**
     * Clear database sessions
     */
    private function clearDatabaseSessions(): int
    {
        try {
            if (DB::getSchemaBuilder()->hasTable('sessions')) {
                $count = DB::table('sessions')->count();
                DB::table('sessions')->truncate();
                $this->line("Cleared {$count} database sessions.");
                return $count;
            }
        } catch (\Exception $e) {
            $this->warn('Database sessions table not found or not accessible.');
        }
        
        return 0;
    }

    /**
     * Clear cache-based sessions
     */
    private function clearCacheSessions(): void
    {
        try {
            Cache::flush();
            $this->line("Cleared cache-based sessions.");
        } catch (\Exception $e) {
            $this->warn('Cache not accessible: ' . $e->getMessage());
        }
    }

    /**
     * Create flag file for middleware to detect force logout
     */
    private function createForceLogoutFlag(): void
    {
        $flagFile = storage_path('framework/force-logout.flag');
        file_put_contents($flagFile, date('Y-m-d H:i:s'));
        $this->line("Created force logout flag for middleware detection.");
    }
}
