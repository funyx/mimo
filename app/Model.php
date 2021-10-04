<?php

namespace Mimo;

use Illuminate\Database\Capsule\Manager;

class Model extends \Illuminate\Database\Eloquent\Model
{
	public function __construct( array $attributes = [] )
	{
		parent::__construct($attributes);
		if(!array_key_exists('eloquent', $_SERVER) ?? $_SERVER['eloquent'] !== true) {
			container()->get(Manager::class);
			$_SERVER['eloquent'] = true;
		}
	}
}
