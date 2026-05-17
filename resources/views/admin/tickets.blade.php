<!DOCTYPE html>
<html>
<head>
    <title>Ticket Management</title>
</head>
<body>

<h2>Support Tickets</h2>

<table border="1">
    <tr>
        <th>ID</th>
        <th>User</th>
        <th>Subject</th>
        <th>Status</th>
        <th>Action</th>
    </tr>

    @foreach($tickets as $ticket)
    <tr>
        <td>{{ $ticket->id }}</td>
        <td>{{ $ticket->user->name }}</td>
        <td>{{ $ticket->subject }}</td>
        <td>{{ $ticket->status }}</td>
        <td>
            <a href="{{ route('admin.ticket.view', $ticket->id) }}">View</a>
        </td>
    </tr>
    @endforeach

</table>

</body>
</html>
