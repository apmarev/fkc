<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Access extends Model {
    use HasFactory;

    protected $table = 'access';

    protected $id;
    protected $name;
    protected $description;
    protected $secret;
    protected $client;
    protected $access;
    protected $refresh;
    protected $expires;

}
