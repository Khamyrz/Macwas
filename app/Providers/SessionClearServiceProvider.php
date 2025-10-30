<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;

class SessionClearServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Only clear sessions in local development environment
        if (app()->environment('local', 'development')) {
            $this->clearAllSessions();
        }
    }

    /**
     * Clear all sessions from all storage methods
     */
    private function clearAllSessions(): void
    {
        try {
            // Clear file-based sessions
            $this->clearFileSessions();
            
            // Clear database sessions if they exist
            $this->clearDatabaseSessions();
            
            // Clear cache-based sessions
            $this->clearCacheSessions();
            
        } catch (\Exception $e) {
            // Log error but don't break the application
            \Log::warning('Failed to clear sessions: ' . $e->getMessage());
        }
    }

    /**
     * Clear file-based sessions
     */
    private function clearFileSessions(): void
    {
        $sessionPath = storage_path('framework/sessions');
        
        if (File::exists($sessionPath)) {
            $files = File::files($sessionPath);
            foreach ($files as $file) {
                if ($file->getExtension() === 'php') {
                    File::delete($file->getPathname());
                }
            }
        }
    }

    /**
     * Clear database sessions
     */
    private function clearDatabaseSessions(): void
    {
        try {
            if (DB::getSchemaBuilder()->hasTable('sessions')) {
                DB::table('sessions')->truncate();
            }
        } catch (\Exception $e) {
            // Table might not exist or database connection might fail
            // This is expected in some environments
        }
    }

    /**
     * Clear cache-based sessions
     */
    private function clearCacheSessions(): void
    {
        try {
            \Cache::flush();
        } catch (\Exception $e) {
            // Cache might not be available
        }
    }
}
