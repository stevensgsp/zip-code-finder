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
     *
     * @OA\Post(
     *     path="/api/zip-codes/crawl",
     *     operationId="crawl",
     *     tags={"Zip Codes"},
     *     summary="Crawl the zip codes and store them in the database.",
     *
     *     @OA\Response(response=200, description="Successful."),
     *     security={}
     * )
     */
    public function crawl()
    {
        CrawlZipCodesData::dispatch();

        return [];
    }

    /**
     * @param  string  $id
     * @return \Illuminate\Http\JsonResponse
     *
     * @OA\Get(
     *     path="/api/zip-codes/{id}",
     *     operationId="show",
     *     tags={"Zip Codes"},
     *     summary="Return the requested zip code.",
     *     @OA\Parameter(name="id", required=true, in="path", @OA\Schema(type="string")),
     *
     *     @OA\Response(response=200, description="Data returned successfully.", @OA\JsonContent(
     *         ref="#/components/schemas/ZipCode"
     *     )),
     *     @OA\Response(response=404, description="Not found."),
     *     security={}
     * )
     */
    public function show($id)
    {
        JsonResource::withoutWrapping();

        $zipCode = ZipCode::where('zip_code', $id)->firstOrFail();

        return new ZipCodeResource($zipCode);
    }
}
