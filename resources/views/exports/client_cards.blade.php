<table>
    <thead>
    <tr>
        <th style="text-align: center;width: 200px;font-weight: 700;">Card Number</th>
        <th style="text-align: center;width: 200px;font-weight: 700;">Last 8 digits</th>
        <th style="text-align: center;width: 200px;font-weight: 700;">Password</th>
    </tr>
    </thead>
    <tbody>
    @foreach($clients as $client)
        <tr>
            <td style="text-align: center;">{{ $client['card_number'] . ' ' }}</td>
            <td style="text-align: center;">{{ $client['username'] }}</td>
            <td style="text-align: center;">{{ $client['password'] }}</td>
        </tr>
    @endforeach
    </tbody>
</table>
















