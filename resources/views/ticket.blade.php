<!-- resources/views/ticket.blade.php

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Détails du Ticket</title>
</head>
<body>
    <h1>Détails du Ticket</h1>

    <div>
        <p><strong>ID du Ticket:</strong> {{ $ticket['id'] }}</p>
        <p><strong>Date d'Achat:</strong> {{ $ticket['purchase_date'] }}</p>
        <p><strong>Date d'Expiration:</strong> {{ $ticket['expiration_date'] }}</p>
        <p><strong>Statut:</strong> {{ $ticket['statut'] }}</p>
        <p><strong>Temps Restant:</strong> {{ $ticket['remaining_time'] }}</p>
        <p><strong>QR Code:</strong></p>
        <div>{!! QrCode::size(150)->generate(route('ticket.show', $ticket['id'])) !!}</div>
    </div>
</body>
</html> -->

<!DOCTYPE html>
<html>
<head>
    <title>Détails du Ticket</title>
</head>
<body>
    <h1>Détails du ticket</h1>

    <p><strong>ID du ticket:</strong> {{ $ticket->id }}</p>
    <p><strong>Date d'achat:</strong> {{ $ticket->created_at }}</p>
    <p><strong>Date d'expiration:</strong> {{ $ticket->expiration_date }}</p>
    <p><strong>Statut:</strong> {{ $ticket->statut }}</p>
    <p><strong>Temps restant:</strong> {{ $ticket->remaining_time }}</p>

    <h2>QR Code:</h2>
    <img src="data:image/svg+xml;base64,{{ base64_encode(QrCode::generate($ticket->id)) }}" alt="QR Code">
</body>
</html>
