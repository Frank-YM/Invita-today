<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class MakeSuperAdmin extends Command
{
    protected $signature = 'user:make-super {email : Email del usuario a promover} {--revoke : Quitar el rol en vez de darlo}';

    protected $description = 'Promueve (o revoca) a un usuario como super admin.';

    public function handle()
    {
        $email = $this->argument('email');
        $user = User::where('email', $email)->first();

        if (!$user) {
            $this->error("No existe usuario con email {$email}");
            return self::FAILURE;
        }

        $revoke = (bool) $this->option('revoke');
        $user->update(['is_super_admin' => !$revoke]);

        $this->info(sprintf(
            '%s ahora %s super admin.',
            $user->email,
            $revoke ? 'NO es' : 'es'
        ));

        return self::SUCCESS;
    }
}
