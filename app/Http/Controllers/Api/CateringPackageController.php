<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\CateringPackageApiResource;
use App\Models\CateringPackage;
use Illuminate\Http\Request;

class CateringPackageController extends Controller
{
    public function index() {
        $cateringPackage    = CateringPackage::with(['city', 'kitchen', 'category', 'tiers', 'tiers.benefits'])->get();
        return CateringPackageApiResource::collection($cateringPackage);
    }

    public function show(CateringPackage $cateringPackage) {
        $cateringPackage->load(['city', 'photos', 'bonuses', 'category', 'kitchen', 'testimonials', 'tiers', 'tiers.benefits']);
        $cateringPackage->kitchen->loadCount('cateringPackages');
        return new CateringPackageApiResource($cateringPackage);
    }
}
