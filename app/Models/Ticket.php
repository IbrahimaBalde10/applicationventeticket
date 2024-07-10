<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

use App\Models\Transaction; // Transaction model
use App\Models\TicketType; // TicketType model

class Ticket extends Model
{
    use HasFactory;

    protected $fillable = [
        'transaction_id', 
        'ticket_type_id',  
        'qr_code',
        'statut',
        'purchase_date',
        'expiration_date'
    ];

    protected $dates = [
        'purchase_date',
        'expiration_date',
    ];
 public function calculateExpirationDate($type)
    {
        $this->purchase_date = Carbon::now();

        if ($type == 'Aller simple') {
            $this->expiration_date = $this->purchase_date->copy()->addHour(2);
        } elseif ($type == 'Aller retour') {
            $this->expiration_date = $this->purchase_date->copy()->addHours(5);
        }

        $this->save();
    }

    public function updateStatut()
    {
        $now = Carbon::now();

        if ($this->expiration_date && $now->greaterThanOrEqualTo($this->expiration_date)) {
            $this->statut = 'expire';
        } else {
            $this->statut= 'valide';
        }

        $this->save();
    }

public function getRemainingTimeAttribute()
    {
        $now = Carbon::now();

        if ($this->expiration_date && is_string($this->expiration_date)) {
            $expirationDate = Carbon::parse($this->expiration_date);
            if ($expirationDate->greaterThan($now)) {
                // return $expirationDate->diffForHumans($now, true);

                return response()->json(['Il vous reste: ' => $expirationDate->diffForHumans($now, true) ]);
            }
        }

        return 'Votre ticket est expiré';
    }


}
