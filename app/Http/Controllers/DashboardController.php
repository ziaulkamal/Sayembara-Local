<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DashboardController extends Controller
{
    public function upload(Request $request)
    {
        if ($request->hasFile('ktp')) {
            $path = $request->file('ktp')->store('uploads/ktp');
        }

        if ($request->hasFile('pdf_file')) {
            $path = $request->file('pdf_file')->store('uploads/pdf');
        }

        for ($i = 1; $i <= 4; $i++) {
            if ($request->hasFile("demo_image_$i")) {
                $request->file("demo_image_$i")->store("uploads/demo_image");
            }
            if ($request->hasFile("other_file_$i")) {
                $request->file("other_file_$i")->store("uploads/other_file");
            }
        }

        return response()->json(['status' => 'success']);
    }
}
