<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CalendarEvent;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Exception;

class CalendarController extends Controller
{
    /**
     * GET: Ambil semua events
     */
    public function index()
    {
        try {
            $events = CalendarEvent::all()->map(function ($event) {
                return [
                    'id' => $event->id,
                    'title' => $event->title,
                    'start' => $event->start->toIso8601String(),
                    'end' => $event->end->toIso8601String(),
                    'allDay' => $event->all_day,
                    'description' => $event->description,
                ];
            });

            return response()->json($events);
            
        } catch (Exception $e) {
            Log::error('Calendar index error: ' . $e->getMessage());
            return response()->json([
                'message' => 'Gagal memuat events',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * POST: Buat event baru
     */
    public function store(Request $request)
    {
        try {
            // Log request untuk debugging
            Log::info('Calendar store request:', $request->all());

            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'start' => 'required|date',
                'end' => 'required|date|after_or_equal:start',
                'allDay' => 'boolean',
                'description' => 'nullable|string',
            ]);

            $event = CalendarEvent::create([
                'title' => $validated['title'],
                'start' => Carbon::parse($validated['start']),
                'end' => Carbon::parse($validated['end']),
                'all_day' => $validated['allDay'] ?? false,
                'description' => $validated['description'] ?? null,
            ]);

            Log::info('Event created successfully:', ['id' => $event->id]);

            return response()->json([
                'id' => $event->id,
                'title' => $event->title,
                'start' => $event->start->toIso8601String(),
                'end' => $event->end->toIso8601String(),
                'allDay' => $event->all_day,
                'description' => $event->description,
            ], 201);
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::warning('Validation error:', $e->errors());
            return response()->json([
                'message' => 'Validation error',
                'errors' => $e->errors()
            ], 422);
            
        } catch (Exception $e) {
            Log::error('Calendar store error: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return response()->json([
                'message' => 'Gagal menyimpan event',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * PUT: Update event
     */
    public function update(Request $request, $id)
    {
        try {
            $event = CalendarEvent::findOrFail($id);
            
            // Log request untuk debugging
            Log::info('Calendar update request:', [
                'id' => $id,
                'data' => $request->all()
            ]);
            
            $validated = $request->validate([
                'title' => 'sometimes|required|string|max:255',
                'start' => 'sometimes|required|date',
                'end' => 'sometimes|required|date|after_or_equal:start',
                'allDay' => 'sometimes|boolean',
                'description' => 'nullable|string',
            ]);

            // Transform data
            if (isset($validated['start'])) {
                $validated['start'] = Carbon::parse($validated['start']);
            }
            if (isset($validated['end'])) {
                $validated['end'] = Carbon::parse($validated['end']);
            }
            if (isset($validated['allDay'])) {
                $validated['all_day'] = $validated['allDay'];
                unset($validated['allDay']);
            }

            $event->update($validated);

            Log::info('Event updated successfully:', ['id' => $event->id]);

            return response()->json([
                'id' => $event->id,
                'title' => $event->title,
                'start' => $event->start->toIso8601String(),
                'end' => $event->end->toIso8601String(),
                'allDay' => $event->all_day,
                'description' => $event->description,
            ]);
            
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::warning('Event not found:', ['id' => $id]);
            return response()->json([
                'message' => 'Event tidak ditemukan'
            ], 404);
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::warning('Validation error:', $e->errors());
            return response()->json([
                'message' => 'Validation error',
                'errors' => $e->errors()
            ], 422);
            
        } catch (Exception $e) {
            Log::error('Calendar update error: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return response()->json([
                'message' => 'Gagal update event',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * DELETE: Hapus event
     */
    public function destroy($id)
    {
    try {
        $event = CalendarEvent::findOrFail($id);
        $event->delete();

        Log::info('Event deleted successfully:', ['id' => $id]);

        return response()->json([
            'message' => 'Event berhasil dihapus'
        ], 200);
        
    } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
        Log::warning('Event not found for deletion:', ['id' => $id]);
        return response()->json([
            'message' => 'Event tidak ditemukan'
        ], 404);
        
    } catch (Exception $e) {
        Log::error('Calendar destroy error: ' . $e->getMessage());
        return response()->json([
            'message' => 'Gagal menghapus event',
            'error' => $e->getMessage()
        ], 500);
    }
    }
}