<div class="responsive-table animated" id="products-table">
    <table>
        <thead>
            <tr>
                <th>Código_Produto</th>
                <th>Descr</th>
                <th>Data Inserido</th>
                <th>Data Alterado</th>
            </tr>
        </thead>
        <tbody id="products-table-body">
            @foreach($products as $product)
                <tr onclick="selectProduct({{ $product->id }})">
                    <td>{{ $product->Código_Produto }}</td>
                    <td>{{ $product->descr }}</td>
                    <td>{{ $product->inserido }}</td>
                    <td>{{ $product->alterado }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <div id="products-pagination" class="pagination-controls">
        <!-- Pagination Controls -->
    </div>
</div>
<script src="{{ asset('js/products-table.js') }}"></script>
<script>console.log('[products-table.blade.php] Products table view loaded');</script>
