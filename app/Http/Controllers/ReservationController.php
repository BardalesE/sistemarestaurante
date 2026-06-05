<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\Table;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ReservationController extends Controller
{
    public function index()
    {
        // Traemos reservas futuras y las de hoy (incluso si pasaron hace unas horas)
        $reservations = Reservation::where('reservation_time', '>=', Carbon::now()->startOfDay()) 
            ->orderBy('reservation_time', 'asc')
            ->with('table')
            ->get();
            
        $tables = Table::where('status', 'available')->get();

        return view('reservations.index', compact('reservations', 'tables'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'client_name'      => 'required|string|max:255',
            'reservation_time' => 'required|date',
            'people'           => 'required|integer|min:1|max:200',
            'table_id'         => 'nullable|exists:tables,id',
            'phone'            => 'nullable|string|max:20',
            'note'             => 'nullable|string|max:500',
        ]);

        Reservation::create($request->only(['client_name', 'phone', 'reservation_time', 'people', 'table_id', 'note']));

        return redirect()->back()->with('success', 'Reserva agendada correctamente.');
    }

    public function updateStatus(Request $request, Reservation $reservation)
    {
        $request->validate(['status' => 'required|in:confirmed,cancelled']);
        $reservation->update(['status' => $request->status]);
        
        $msg = $request->status == 'confirmed' ? 'Reserva confirmada.' : 'Reserva cancelada.';
        return redirect()->back()->with('success', $msg);
    }

    public function destroy(Reservation $reservation)
    {
        $reservation->delete();
        return redirect()->back()->with('success', 'Reserva eliminada.');
    }
}