<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

/**
 * This class should be parent class for other API controllers
 * Class Controller
 *
 * @OA\Info(title="Zip Code Finder API", version="1.0")
 *
 * @OA\Server(
 *     url="https://url.local",
 *     description="DEV Server"
 * )
 */
class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
}
