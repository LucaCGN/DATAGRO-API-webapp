// Function to load data series for a product with pagination
window.loadDataSeries = function(productCode, page = 1, perPage = 10) {
    console.log(`Initiating fetch to /data-series/${productCode}?page=${page}&perPage=${perPage}`);

    fetch(`/data-series/${productCode}?page=${page}&perPage=${perPage}`)
        .then(response => {
            console.log("Raw fetch response:", response);
            if (!response.ok) {
                console.error(`[DataSeriesTable] Error fetching data series: ${response.statusText}`);
                throw new Error(`Error fetching data series: ${response.statusText}`);
            }
            return response.json();
        })
        .then(data => {
            console.log("[DataSeriesTable] DataSeries API Response received:", data);
            let tableBody = document.getElementById('data-series-body');
            tableBody.innerHTML = data.map(series => `
                <tr>
                    <td>${series.cod}</td>
                    <td>${series.data}</td>
                    <td>${series.ult}</td>
                    <td>${series.mini}</td>
                    <td>${series.maxi}</td>
                    <td>${series.abe}</td>
                    <td>${series.volumes}</td>
                    <td>${series.med}</td>
                    <td>${series.aju}</td>
                </tr>
            `).join('');
            console.log("[DataSeriesTable] Data series successfully rendered in table");

            // Update this function if pagination data is included in the response
            // or handle pagination differently if the response structure is different
            renderDataSeriesPagination(data, productCode);
        })
        .catch(error => {
            console.error("Fetch request failed: ", error);
        });
};


// Function to render pagination for data series
// Ensure the renderPagination is specific for DataSeries and does not overlap with ProductsTable
function renderDataSeriesPagination(paginationData, productCode) {
    let paginationDiv = document.getElementById('data-series-pagination');
    paginationDiv.innerHTML = ''; // Clear existing pagination controls

    // Previous button
    if (paginationData.current_page > 1) {
        paginationDiv.innerHTML += `<button onclick="loadDataSeries(${productCode}, ${paginationData.current_page - 1}, ${paginationData.per_page})">Previous</button>`;
    }

    // Current Page Indicator
    paginationDiv.innerHTML += `<span>Page ${paginationData.current_page} of ${paginationData.last_page}</span>`;

    // Next button
    if (paginationData.current_page < paginationData.last_page) {
        paginationDiv.innerHTML += `<button onclick="loadDataSeries(${productCode}, ${paginationData.current_page + 1}, ${paginationData.per_page})">Next</button>`;
    }
}

// Call this function wherever you need to render pagination for data series
