<?php

namespace App;

class Model extends \Illuminate\Database\Eloquent\Model
{
    public function __construct(array $attributes = []) {
        // init the db connection
        eloquent();
        parent::__construct( $attributes );
    }
}
