<!-- Data Series Table -->
<div class="responsive-table animated" id="data-series-table">
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
                <th>med</th>
                <th>aju</th>
            </tr>
        </thead>
        <tbody id="data-series-body">
            <!-- Data populated by DataSeriesTable.js -->
        </tbody>
    </table>
</div>
<div id="data-series-pagination" class="pagination-controls">
    <!-- Pagination Controls populated by DataSeriesTable.js -->
</div>
<script src="{{ asset('js/DataSeriesTable.js') }}"></script>
<script>console.log('[data-series-table.blade.php] Data series table view loaded');</script>
