function loadDataSeries(productId, page = 1, perPage = 10) {
    console.log(`Loading data series for productId: ${productId}, page: ${page}, perPage: ${perPage}`);  // Log the function call
    fetch(`/data-series/${productId}?page=${page}&perPage=${perPage}`)
        .then(response => {
            if (!response.ok) {
                console.error(`Error fetching data series: ${response.statusText}`);
            }
            return response.json();
        })
        .then(data => {
            console.log("DataSeries API Response:", data);  // Log API response
            let tableBody = document.getElementById('data-series-body');
            tableBody.innerHTML = data.series.map(series => `
                <tr>
                    <td>${series.name}</td>
                    <td>${series.value}</td>
                    <td>${series.date}</td>
                </tr>
            `).join('');

            renderPagination(data.pagination, (newPage) => loadDataSeries(productId, newPage));
        })
        .catch(error => console.error("DataSeries API Error:", error)); // Log any fetch errors
}

function renderPagination(paginationData, updateFunction) {
    console.log("Rendering pagination with data:", paginationData);  // Log pagination data
    let paginationDiv = document.getElementById('data-series-pagination');
    paginationDiv.innerHTML = '';

    if (paginationData.currentPage > 1) {
        let prevButton = document.createElement('button');
        prevButton.textContent = 'Previous';
        prevButton.onclick = () => {
            console.log(`Previous page button clicked, going to page ${paginationData.currentPage - 1}`);  // Log button click
            updateFunction(paginationData.currentPage - 1);
        };
        paginationDiv.appendChild(prevButton);
    }

    if (paginationData.currentPage < paginationData.lastPage) {
        let nextButton = document.createElement('button');
        nextButton.textContent = 'Next';
        nextButton.onclick = () => {
            console.log(`Next page button clicked, going to page ${paginationData.currentPage + 1}`);  // Log button click
            updateFunction(paginationData.currentPage + 1);
        };
        paginationDiv.appendChild(nextButton);
    }
}
