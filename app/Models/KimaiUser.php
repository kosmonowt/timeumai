<?php

namespace App\Models;

class KimaiUser {

    protected int $id;

    public function __construct(int $id) {
        $this->id = $id;
    }

    public static function make(int $id) : KimaiUser {
        return new static($id);
    }

    public function getId() : int {
        return $this->id;
    }

}
