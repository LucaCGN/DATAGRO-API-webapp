<div>
    <table>
        <thead>
            <tr>
                <th>Data Series Name</th>
                <th>Value</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            @foreach($dataSeries as $series)
            <tr>
                <td>{{ $series['name'] }}</td>
                <td>{{ $series['value'] }}</td>
                <td>{{ $series['date'] }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
