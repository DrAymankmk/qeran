<?php

namespace App\Http\Controllers\Api\V1\Home;

use App\Helpers\Constant;
use App\Http\Controllers\Controller;
use App\Http\Resources\Category\CategoryResource;
use App\Models\Category;
use App\Services\RespondActive;
use Illuminate\Http\Request;

class GetHome extends Controller
{
    public function __invoke(Request $request)
    {
//        $category = Category::create([
//                'ar'        => [
//                    'name'          => 'زفاف',
//                    'slug'          => 'زفاف',
//                ],
//                'en'        => [
//                    'name'          => 'Wedding',
//                    'slug'          => 'Wedding',
//                ],
//                'is_wedding' => 1 ,
//            ]);
//
//                storeImage([
//                    'value' => $request->image,
//                    'folderName' => Constant::CATEGORY_IMAGE_FOLDER_NAME,
//                    'model' => $category,
//                    'saveInDatabase' => true
//                ]);



        return RespondActive::success('The action ran successfully!',CategoryResource::collection(Category::GetActiveCategories()->paginate())->response()->getData());
    }
}
