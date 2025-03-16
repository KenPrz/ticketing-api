<?php

return [
    'otp_expires' => (int) env('OTP_EXPIRES', 5),
    // Defaults to 50 kilometers
    'default_radius' => (int) env('DEFAULT_RADIUS', 50),
    // Defaults to Metro Manila
    'default_coordinates' => [
        'latitude' => (float) env('DEFAULT_LATITUDE', 14.5995),
        'longitude' => (float) env('DEFAULT_LONGITUDE', 120.9842),
    ],
];