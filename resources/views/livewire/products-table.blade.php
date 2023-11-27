<div class="responsive-table">
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
                <tr wire:click="selectProduct({{ $product->id }})">
                    <td>{{ $product->nome }}</td>
                    <td>{{ $product->freq }}</td>
                    <td>{{ $product->inserido }}</td>
                    <td>{{ $product->alterado }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <!-- Custom Pagination Controls -->
    <div>
        @if($products->onFirstPage())
            <button disabled>Previous</button>
        @else
            <button wire:click="previousPage">Previous</button>
        @endif

        <span>Page {{ $products->currentPage() }} of {{ $products->lastPage() }}</span>

        @if($products->hasMorePages())
            <button wire:click="nextPage">Next</button>
        @else
            <button disabled>Next</button>
        @endif
    </div>
    <!-- Default Livewire Pagination Links -->
    {{ $products->links() }}
</div>
