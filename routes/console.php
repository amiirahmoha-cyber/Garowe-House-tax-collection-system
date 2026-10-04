<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// NOTE (Laravel 9): the daily SMS reminder schedule is registered in
// app/Console/Kernel.php (the schedule() method), NOT here.
// This differs from Laravel 11, which uses Schedule::command() directly
// in this file. If you later upgrade to Laravel 11, move it back here.
