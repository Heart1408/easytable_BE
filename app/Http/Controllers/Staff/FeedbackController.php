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

  public function change_status($id)
  {
    try {
      $feedback = Booking::findOrFail($id);
      $feedback->update(['feedback_status' => Booking::FEEDBACK_STATUS['seen'], 'updated_at' => false]);

      return response()->json([
        'success' => true,
        'message' => 'Cập nhật thành công!',
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