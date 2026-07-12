<?php

namespace App\Http\Controllers;

use App\Models\Itinerary;
use App\Models\ItineraryDay;
use App\Models\ItineraryActivity;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class ItineraryController extends Controller
{
    public function index(Request $request)
    {
        $query = Itinerary::with('user')->latest();

        if (Auth::user()->role !== 'super_admin') {
            $query->where('user_id', Auth::id());
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $itineraries = $query->paginate(10)->withQueryString();
        return view('itineraries.index', compact('itineraries'));
    }

    public function create()
    {
        $users = User::orderBy('name')->get();
        return view('itineraries.create', compact('users'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'status' => ['required', 'string', 'in:draft,active,done'],
            'days' => ['required', 'array', 'min:1'],
            'days.*.day_number' => ['required', 'integer'],
            'days.*.date' => ['required', 'date'],
            'days.*.activities' => ['required', 'array', 'min:1'],
            'days.*.activities.*.time_from' => ['required', 'date_format:H:i'],
            'days.*.activities.*.time_to' => ['required', 'date_format:H:i'],
            'days.*.activities.*.activity' => ['required', 'string'],
        ]);

        DB::transaction(function () use ($data) {
            $itinerary = Itinerary::create([
                'user_id' => $data['user_id'],
                'title' => $data['title'],
                'description' => $data['description'] ?? null,
                'start_date' => $data['start_date'],
                'end_date' => $data['end_date'],
                'status' => $data['status'],
            ]);

            foreach ($data['days'] as $dayIndex => $dayData) {
                $day = ItineraryDay::create([
                    'itinerary_id' => $itinerary->id,
                    'day_number' => $dayData['day_number'],
                    'date' => $dayData['date'],
                ]);

                foreach ($dayData['activities'] as $actIndex => $actData) {
                    ItineraryActivity::create([
                        'itinerary_day_id' => $day->id,
                        'time_from' => $actData['time_from'],
                        'time_to' => $actData['time_to'],
                        'activity' => $actData['activity'],
                        'sort_order' => $actIndex,
                    ]);
                }
            }
        });

        return redirect()->route('itineraries.index')->with('success', 'Itinerary created successfully.');
    }

    public function edit(Itinerary $itinerary)
    {
        $itinerary->load('days.activities');
        $users = User::orderBy('name')->get();
        return view('itineraries.edit', compact('itinerary', 'users'));
    }

    public function update(Request $request, Itinerary $itinerary)
    {
        $data = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'status' => ['required', 'string', 'in:draft,active,done'],
            'days' => ['required', 'array', 'min:1'],
            'days.*.day_number' => ['required', 'integer'],
            'days.*.date' => ['required', 'date'],
            'days.*.activities' => ['required', 'array', 'min:1'],
            'days.*.activities.*.time_from' => ['required', 'date_format:H:i'],
            'days.*.activities.*.time_to' => ['required', 'date_format:H:i'],
            'days.*.activities.*.activity' => ['required', 'string'],
        ]);

        DB::transaction(function () use ($itinerary, $data) {
            $itinerary->update([
                'user_id' => $data['user_id'],
                'title' => $data['title'],
                'description' => $data['description'] ?? null,
                'start_date' => $data['start_date'],
                'end_date' => $data['end_date'],
                'status' => $data['status'],
            ]);

            $itinerary->days()->each(function ($day) {
                $day->activities()->delete();
                $day->delete();
            });

            foreach ($data['days'] as $dayIndex => $dayData) {
                $day = ItineraryDay::create([
                    'itinerary_id' => $itinerary->id,
                    'day_number' => $dayData['day_number'],
                    'date' => $dayData['date'],
                ]);

                foreach ($dayData['activities'] as $actIndex => $actData) {
                    ItineraryActivity::create([
                        'itinerary_day_id' => $day->id,
                        'time_from' => $actData['time_from'],
                        'time_to' => $actData['time_to'],
                        'activity' => $actData['activity'],
                        'sort_order' => $actIndex,
                    ]);
                }
            }
        });

        return redirect()->route('itineraries.index')->with('success', 'Itinerary updated successfully.');
    }

    public function destroy(Itinerary $itinerary)
    {
        $itinerary->delete();
        return redirect()->route('itineraries.index')->with('success', 'Itinerary deleted successfully.');
    }

    public function pdf(Itinerary $itinerary)
    {
        $itinerary->load('user', 'days.activities');

        $coverPath = public_path('cover-itinerary.png');
        $coverBase64 = '';
        if (file_exists($coverPath)) {
            $img = imagecreatefrompng($coverPath);
            $origW = imagesx($img);
            $origH = imagesy($img);

            $targetW = 1200;
            $scale = $targetW / $origW;
            $targetH = (int)($origH * $scale);

            $resized = imagecreatetruecolor($targetW, $targetH);
            imagealphablending($resized, false);
            imagesavealpha($resized, true);
            imagecopyresampled($resized, $img, 0, 0, 0, 0, $targetW, $targetH, $origW, $origH);
            imagedestroy($img);

            $white = imagecolorallocatealpha($resized, 255, 255, 255, 0);
            $whiteDim = imagecolorallocatealpha($resized, 255, 255, 255, 40);

            $fontMain = public_path('fonts/segoeui.ttf');
            $fontBold = public_path('fonts/segoeuib.ttf');

            $name = $itinerary->user->name;
            $website = 'www.bandungdrivertour.com';
            $label = 'Prepared by:';
            $title = 'Travel Itinerary';
            $dateRange = $itinerary->start_date->format('d M Y') . ' — ' . $itinerary->end_date->format('d M Y');

            $centerX = $targetW / 2;

            $titleSize = (int)(52 * $scale);
            $fontItalic = public_path('fonts/segoeuiz.ttf');
            $titleBox = imagettfbbox($titleSize, 0, $fontItalic, $title);
            $titleW = $titleBox[2] - $titleBox[0];
            imagettftext($resized, $titleSize, 0, $centerX - ($titleW / 2), $targetH - (int)(440 * $scale), $white, $fontItalic, $title);

            $dateSize = (int)(26 * $scale);
            $dateBox = imagettfbbox($dateSize, 0, $fontMain, $dateRange);
            $dateW = $dateBox[2] - $dateBox[0];
            imagettftext($resized, $dateSize, 0, $centerX - ($dateW / 2), $targetH - (int)(395 * $scale), $whiteDim, $fontMain, $dateRange);

            $labelSize = (int)(28 * $scale);
            $labelBox = imagettfbbox($labelSize, 0, $fontMain, $label);
            $labelW = $labelBox[2] - $labelBox[0];
            imagettftext($resized, $labelSize, 0, $centerX - ($labelW / 2), $targetH - (int)(345 * $scale), $whiteDim, $fontMain, $label);

            $nameSize = (int)(42 * $scale);
            $nameBox = imagettfbbox($nameSize, 0, $fontBold, $name);
            $nameW = $nameBox[2] - $nameBox[0];
            imagettftext($resized, $nameSize, 0, $centerX - ($nameW / 2), $targetH - (int)(295 * $scale), $white, $fontBold, $name);

            $webSize = (int)(24 * $scale);
            $webBox = imagettfbbox($webSize, 0, $fontMain, $website);
            $webW = $webBox[2] - $webBox[0];
            imagettftext($resized, $webSize, 0, $centerX - ($webW / 2), $targetH - (int)(250 * $scale), $whiteDim, $fontMain, $website);

            ob_start();
            imagejpeg($resized, null, 65);
            $jpegData = ob_get_clean();
            imagedestroy($resized);

            $coverBase64 = 'data:image/jpeg;base64,' . base64_encode($jpegData);
        }

        $pdf = Pdf::loadView('itineraries.pdf', compact('itinerary', 'coverBase64'))
            ->setPaper('a4', 'portrait')
            ->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled'      => false,
                'isFontSubsettingEnabled' => true,
                'isPhpTimeoutEnabled'  => false,
                'defaultFont'          => 'sans-serif',
            ]);

        return $pdf->download('itinerary-' . $itinerary->id . '.pdf');
    }
}
