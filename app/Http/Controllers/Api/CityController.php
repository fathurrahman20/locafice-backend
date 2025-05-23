<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\CityResource;
use App\Models\City;
use Illuminate\Http\Request;

class CityController extends Controller
{
    public function index(): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        $cities = City::withCount('officeSpaces')->get();
        return CityResource::collection($cities);
    }

    public function show(City $city): CityResource
    {
        $city->load(['officeSpaces.city', 'officeSpaces.photos']);
        $city->loadCount('officeSpaces');
        return new CityResource($city);
    }
}
