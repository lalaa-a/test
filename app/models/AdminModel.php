<?php

require_once __DIR__ . '/ModeratorModel.php';

class AdminModel extends ModeratorModel
{
    // Thin adapter so Admin.php can load an admin-named model
    // while reusing the existing ModeratorModel implementation.
}
