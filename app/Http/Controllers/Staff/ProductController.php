<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use TheSeer\Tokenizer\Exception;
use Illuminate\Support\Facades\Validator;
use App\Models\Dish;
use App\Models\DishType;

class ProductController extends Controller
{
  public function get_list(Request $request)
  {

    try {
      $categoryId = $request->get('categoryId');
      $search = $request->get('search');


      $data = Dish::where("dish_type_id", $categoryId)->get();
      

      $result = [
        'success' => true,
        'data' => $data,
      ];
    } catch (Exception $e) {
      $result = [
        'status' => 500,
        'success' => false,
        'message' => $e->getMessage(),
      ];
    }

    return response()->json($result);
  }
}
