<?php

return [
  /*
  |--------------------------------------------------------------------------
  | Expiration Minutes
  |--------------------------------------------------------------------------
  */

  'expiration' => config('JWT_EXPIRATION_IN_MINUTES') ?? 60, // Token expires after 60 minutes (1 hours)
];
