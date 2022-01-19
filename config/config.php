<?php

/**
 * The file is part of Lava-maker
 *
 * Lavamake
 *
 */
return [
    'foreign_key' => env('LAVAMAKE_FOREIGN_KEY','user_id'),

    'website' => [
        /**
         * 是否开启网站端缓存
         */
        "cache" => env('LAVAMAKE_WEBSITE_CACHE', false),

        /**
         * 默认缓存有效时间,单位：秒，为0表示永久
         */
        "cache_ttl" => env('LAVAMAKE_WEBSITE_CACHE_TTL', 1000),
    ]
];
