<?php

use Faker\Generator as Faker;

$factory->define(App\Models\Topic::class, function (Faker $faker) {
    // 生成小段文字
    $sentence = $faker->sentence();
    // 随机取一个月内的时间
    $updated_at = $faker->dateTimeThisMonth();
    // 添加参数为：创建的时间永远不会超过参数时间
    $created_at = $faker->dateTimeThisMonth($updated_at);

    return [
        'title' => $sentence,
        'body' => $faker->text(),   // 生成大段文字
        'excerpt' => $sentence,
        'created_at' => $created_at,
        'updated_at' => $updated_at,
    ];
});
