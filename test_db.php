<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

tenancy()->initialize('bdt-restaurant');
$user = App\Models\User::where('email', 'admin@bdt-restaurant.com')->first();
if ($user) {
    echo "Found user: " . $user->email . "\n";
    echo "Password hash: " . $user->password . "\n";
    echo "Matches 'password'? " . (Hash::check('password', $user->password) ? 'YES' : 'NO') . "\n";
} else {
    echo "User NOT found!\n";
}
