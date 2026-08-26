<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Admin Notification Recipient
    |--------------------------------------------------------------------------
    |
    | Initial delivery is intentionally limited to one manually configured
    | Gmail address. The value belongs only in the environment file.
    |
    */
    'recipient' => env('ADMIN_NOTIFICATION_EMAIL'),
];
