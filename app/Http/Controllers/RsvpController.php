<?php

namespace App\Http\Controllers;

use App\Models\Rsvp;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class RsvpController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'attending' => ['required', Rule::in('yes', 'no')],
            'ceremony_attendance' => [
                Rule::requiredIf($request->input('attending') === 'yes'),
                'nullable',
                Rule::in('send_bride', 'receive_bride', 'no'),
            ],
        ]);

        if (($validated['attending'] ?? '') === 'no') {
            $validated['ceremony_attendance'] = null;
        }

        Rsvp::create($validated);

        return response()->json(['message' => 'Cảm ơn bạn đã xác nhận!']);
    }
}
