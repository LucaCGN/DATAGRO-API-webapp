
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
            <!-- Placeholder content -->
            <tr>
                <td>Product 1</td>
                <td>Daily</td>
                <td>2023-01-01</td>
                <td>2023-01-05</td>
            </tr>
            <tr>
                <td>Product 2</td>
                <td>Weekly</td>
                <td>2023-01-02</td>
                <td>2023-01-06</td>
            </tr>
        </tbody>
    </table>
    <!-- Pagination controls -->
    <div>
        <button wire:click="previousPage">Previous</button>
        <button wire:click="nextPage">Next</button>
    </div>
</div>
