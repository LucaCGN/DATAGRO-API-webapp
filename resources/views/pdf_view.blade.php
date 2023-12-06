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
    <h2>Produto Escolhido:</h2>
    <table>
        <thead>
            <tr>
                <th>Produto</th>
                <th>Nome</th>
                <th>Frequência</th>
                <th>Primeira Data</th>
            </tr>
        </thead>
        <tbody>
            @foreach($products as $product)
                <tr>
                    @foreach($product as $field)
                        <td>{{ $field }}</td>
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>

    <h2>Data Series:</h2>
    <table>
        <thead>
            <tr>
                <th>Cod</th>
                <th>data</th>
                <th>ult</th>
                <th>mini</th>
                <th>maxi</th>
                <th>abe</th>
                <th>volumes</th>
            </tr>
        </thead>
        <tbody>
            @foreach($dataSeries as $series)
                <tr>
                    @foreach($series as $field)
                        <td>{{ $field }}</td>
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
