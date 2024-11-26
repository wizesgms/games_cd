<?php

return [
    'disable' => env('CAPTCHA_DISABLE', false),
    'characters' => ['1', '2', '3', '4', '5', '6', '7', '8', '9', '0'],
    'default' => [
        'length' => 5,
        'width' => 135,
        'height' => 40,
        'quality' => 100,
        'math' => false,
        'expire' => 60,
        'encrypt' => false,
        'bgImage' => false,
        'fontColors' => ['#2c3e50'],
    ],
    'math' => [
        'length' => 9,
        'width' => 120,
        'height' => 36,
        'quality' => 90,
        'math' => true,
    ],

    'flat' => [
        'length' => 6,
        'width' => 160,
        'height' => 46,
        'quality' => 90,
        'lines' => 6,
        'bgImage' => false,
        'bgColor' => '#ecf2f4',
        'fontColors' => ['#2c3e50', '#c0392b', '#16a085', '#c0392b', '#8e44ad', '#303f9f', '#f57c00', '#795548'],
        'contrast' => -5,
    ],
    'mini' => [
        'length' => 3,
        'width' => 60,
        'height' => 32,
    ],
    'inverse' => [
        'length' => 5,
        'width' => 120,
        'height' => 36,
        'quality' => 90,
        'sensitive' => true,
        'angle' => 12,
        'sharpen' => 10,
        'blur' => 2,
        'invert' => true,
        'contrast' => -5,
    ]
];
