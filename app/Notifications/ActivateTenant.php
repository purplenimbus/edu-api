<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Bus\Queueable;

class ActivateTenant extends VerifyEmail
{
  use Queueable;
}
