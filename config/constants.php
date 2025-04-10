<?php

return [
    'home_limit' => (int) env('HOME_LIMIT', 7),
    'pagination_limit' => (int) env('PAGINATION_LIMIT', 10),
    'otp_expires' => (int) env('OTP_EXPIRES', 5),
    // Defaults to 50 kilometers
    'default_radius' => (int) env('DEFAULT_RADIUS', 50),
    // Defaults to Metro Manila
    'default_coordinates' => [
        'latitude' => (float) env('DEFAULT_LATITUDE', 14.5995),
        'longitude' => (float) env('DEFAULT_LONGITUDE', 120.9842),
    ],
    'notification_limit' => (int) env('NOTIFICATION_LIMIT', 100),
];