<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Http\Kernel::class);
$request = \Illuminate\Http\Request::capture();
$kernel->handle($request);

use App\Models\User;

// Find user by email
$user = User::where('email', 'syahir@gmail.com')->first();

if (!$user) {
    echo "[✗] User with email 'syahir@gmail.com' not found.\n";
    exit(1);
}

// Make user admin
$user->is_admin = true;
$user->save();

echo "[✓] ✅ User '{$user->name}' ({$user->email}) is now an ADMIN!\n";
echo "[✓] is_admin value: " . ($user->is_admin ? 'TRUE' : 'FALSE') . "\n";
?>


