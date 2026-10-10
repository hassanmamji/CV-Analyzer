<?php

namespace App\Http\Controllers;

use App\Models\CvAnalysis;
use App\Services\CvAnalyzerService;
use App\Services\CvTextExtractor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Throwable;

class CvAnalysisController extends Controller
{
    public function create()
    {
        return view('cv.upload');
    }

    public function store(Request $request, CvTextExtractor $extractor, CvAnalyzerService $analyzer)
    {
          set_time_limit(20);
        $request->validate([
            'cv'       => 'required|file|mimes:pdf|max:5120',
            'job_role' => 'nullable|string|max:100',
        ]);

        try {
            $file = $request->file('cv');
            $path = $file->store('cvs'); // random naam se save hota hai

            $text   = $extractor->extract(Storage::path($path));
            $result = $analyzer->analyze($text, $request->job_role);

            $analysis = CvAnalysis::create([
                'original_name'  => $file->getClientOriginalName(),
                'file_path'      => $path,
                'extracted_text' => $text,
                'job_role'       => $request->job_role,
                'score'          => (int) $result['score'],
                'result'         => $result,
            ]);

            return redirect()->route('cv.show', $analysis);
        } catch (Throwable $e) {
            return back()->withErrors(['cv' => $e->getMessage()]);
        }
    }

    public function show(CvAnalysis $analysis)
    {
        return view('cv.result', compact('analysis'));
    }
}