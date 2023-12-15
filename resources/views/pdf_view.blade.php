<!DOCTYPE html>
<html>
<head>
    <title>Export PDF</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid black;
            padding: 5px;
            text-align: left;
        }
    </style>
</head>
<body>
    <h2>Produto Escolhido: {{ $selectedProduct['longo'] ?? 'N/A' }}</h2>
    <table>
        <thead>
            <tr>
                <th>Produto</th>
                <th>Subproduto</th>
                <th>Local</th>
                <th>Frequência</th>
            </tr>
        </thead>
        <tbody>
            <!-- Since it's a single product, no need for a loop here -->
            <tr>
                <td>{{ $selectedProduct['Produto'] ?? 'N/A' }}</td>
                <td>{{ $selectedProduct['Subproduto'] ?? 'N/A' }}</td>
                <td>{{ $selectedProduct['Local'] ?? 'N/A' }}</td>
                <td>{{ $selectedProduct['Frequência'] ?? 'N/A' }}</td>
            </tr>
        </tbody>
    </table>

    <h2>Data Series:</h2>
    <table>
        <thead>
            <tr>
                <th>Cod</th>
                <th>data</th>
                <th>abe</th>
                <th>maxi</th>
                <th>mini</th>
                <th>ult</th>
                <th>volumes</th>
            </tr>
        </thead>
        <tbody>
            @foreach($dataSeries as $series)
                <tr>
                    <td>{{ $series['cod'] ?? 'N/A' }}</td>
                    <td>{{ $series['data'] ?? 'N/A' }}</td>
                    <td>{{ $series['ult'] ?? 'N/A' }}</td>
                    <td>{{ $series['mini'] ?? 'N/A' }}</td>
                    <td>{{ $series['maxi'] ?? 'N/A' }}</td>
                    <td>{{ $series['abe'] ?? 'N/A' }}</td>
                    <td>{{ $series['volumes'] ?? 'N/A' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
