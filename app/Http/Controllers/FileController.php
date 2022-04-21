<?php

namespace App\Http\Controllers;

use App\Http\Requests\FileRequest;
use Illuminate\Http\Request;

class FileController extends Controller
{
    /**
     * Post File
     *
     * @param  \App\Http\Requests\FileRequest  $request
     */
    public function file(FileRequest $request)
    {
        $fileName = basename($request->file('file')->store('temporary'));

        return response()->json(['file' => $fileName], 200);
    }
}
