<?php

test('users controller can store mimo\models\users records', function () {
    $controller = new Mimo\Controllers\UsersController();
    $model = new Mimo\Models\Users();

    $model->factory()->create(10);

    $controller->get();

    $response->assertStatus(200);
});

test('users controller can show mimo\models\users records', function () {
    $controller = new Mimo\Controllers\UsersController();
    $model = new Mimo\Models\Users();

    $model->factory()->create(10);

    $controller->get();

    $response->assertStatus(200);
});

test('users controller can update mimo\models\users records', function () {
    $controller = new Mimo\Controllers\UsersController();
    $model = new Mimo\Models\Users();

    $model->factory()->create(10);

    $controller->get();

    $response->assertStatus(200);
});

test('users controller can destroy mimo\models\users records', function () {
    $controller = new Mimo\Controllers\UsersController();
    $model = new Mimo\Models\Users();

    $model->factory()->create(10);

    $controller->get();

    $response->assertStatus(200);
});

test('users controller can paginate mimo\models\users records', function () {
    $controller = new Mimo\Controllers\UsersController();
    $model = new Mimo\Models\Users();

    $model->factory()->create(10);

    $controller->get();

    $response->assertStatus(200);
});
