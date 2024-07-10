<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Subscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'subscription_type_id',
        'start_date',
        'end_date',
        'qr_code',
        'statut',
    ];

     protected $dates = [
        'end_date',
        'end_date',
    ];

    
    // Méthode pour calculer la date de fin en fonction du type d'abonnement
    public function calculateEndDate($subscriptionType)
    {
        // Assigner la date de début à maintenant si elle n'est pas déjà définie
        $this->start_date = $this->start_date ?: Carbon::now();

        // Calculer la date de fin en fonction du type d'abonnement
        switch ($subscriptionType) {
            case 'Hebdomadaire':
                $this->end_date = $this->start_date->copy()->addWeek(); // Ajouter une semaine
                break;
            case 'Mensuelle':
                $this->end_date = $this->start_date->copy()->addMonth(); // Ajouter un mois
                break;
            case 'Annuelle':
                $this->end_date = $this->start_date->copy()->addYear(); // Ajouter un an
                break;
            default:
                // Gestion d'un type d'abonnement inconnu
                throw new \InvalidArgumentException("Type d'abonnement inconnu : $subscriptionType");
        }

        // Enregistrer les modifications dans la base de données
        $this->save();
    }


     // Méthode pour mettre à jour le statut de l'abonnement
    public function updateStatut()
    {
        $now = Carbon::now();

        if ($this->end_date && $now->greaterThanOrEqualTo($this->end_date)) {
            $this->statut = 'expire';
        } else {
            $this->statut = 'valide';
        }

        $this->save();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function subscriptionType()
    {
        return $this->belongsTo(SubscriptionType::class);
    }
}
