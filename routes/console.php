<?php

use App\Console\Commands\DemoResetCommand;
use Illuminate\Support\Facades\Schedule;

Schedule::command(DemoResetCommand::class)->daily();
