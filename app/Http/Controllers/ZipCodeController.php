<?php

namespace App\Http\Controllers;

use App\Actions\CrawlZipCodesData;
use App\Http\Resources\ZipCodeResource;
use App\Models\ZipCode;
use Illuminate\Http\Resources\Json\JsonResource;

class ZipCodeController extends Controller
{
    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function crawl()
    {
        CrawlZipCodesData::dispatch();

        return [];
    }

    /**
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        JsonResource::withoutWrapping();

        $zipCode = ZipCode::where('zip_code', $id)->first();

        return new ZipCodeResource($zipCode);
    }
}
