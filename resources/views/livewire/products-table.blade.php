<div>
    <!-- Products Table with pagination -->
    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>Frequency</th>
                <th>Insert Date</th>
                <th>Last Update</th>
            </tr>
        </thead>
        <tbody>
            @foreach($products as $product)
                <tr>
                    <td>{{ $product->nome }}</td>
                    <td>{{ $product->freq }}</td>
                    <td>{{ $product->inserido }}</td>
                    <td>{{ $product->alterado }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    {{ $products->links() }}
</div>
