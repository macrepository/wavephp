<?php

namespace App\User\Controller;

use Base\Views\Render;

class Index {

    const VIEWS = "User/view/user";

    public function create() {
        return Render::views(self::VIEWS, ["user" => [1,2,3,4,5]]);
    }
}