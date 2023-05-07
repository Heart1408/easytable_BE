<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use App\Models\Booking;

class FeedbackController extends Controller
{
  public function get_list(Request $request)
  {
    try {
      $data = $request->all();
      $data['chainstore_id'] = $request->user()->chain_store_id;
      $feedback = Booking::feedbackSearch($data);

      return response()->json([
        'success' => true,
        'data' => $feedback,
      ]);
    } catch (Exception $e) {
      return response()->json([
        'status' => 500,
        'success' => false,
        'message' => $e->getMessage(),
      ]);
    }
  }
}