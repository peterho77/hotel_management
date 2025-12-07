<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use App\Models\Review;


class ReviewController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'general_review' => 'required',
            'rating' => 'required|integer',
            'positive' => 'required',
            'negative' => 'required',
            'booking_id' => 'required'
        ]);

        $newReview = Review::create([
            'general_review' => $request->general_review,
            'rating' => $request->rating,
            'positive' => $request->positive,
            'negative' => $request->negative,
            'booking_id' => $request->booking_id,
        ]);

        // upload room type images
        $folderName = Str::slug($request->booking_id); // "Deluxe Room" -> "deluxe-room"
        $path = "/uploads/customers/booking_review/{$folderName}";

        $imagePaths = [];

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                $filename = $file->getClientOriginalName(); // giữ nguyên tên file
                $storedPath = $file->storeAs($path, $filename, 'public'); // => storage/app/public/room-type/deluxe-room/filename.jpg
                $imagePaths[] = [
                    'path' => $storedPath,
                    'filename' => $filename
                ]; // lưu lại đường dẫn 
            }
        }

        foreach ($imagePaths as $index => $img) {
            $newReview->images()->create([
                'path' => $img['path'],
                'is_featured' => $index === 0,
                'sort_order' => $index + 1,
                'alt_text' => $img['filename'],
            ]);
        };

        $username = Auth::user()->user_name;

        return redirect()->route('user.booking-history', [
            'user_name' => $username
        ]);
    }
}
