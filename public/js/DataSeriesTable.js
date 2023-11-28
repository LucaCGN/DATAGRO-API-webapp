// Function to load data series for a product with pagination
function loadDataSeries(productId, page = 1, perPage = 10) {
    console.log(`Loading data series for productId: ${productId}, page: ${page}, perPage: ${perPage}`);
    fetch(`/data-series/${productId}?page=${page}&perPage=${perPage}`)
        .then(response => {
            if (!response.ok) {
                throw new Error(`Error fetching data series: ${response.statusText}`);
            }
            return response.json();
        })
        .then(data => {
            console.log("DataSeries API Response:", data);
            let tableBody = document.getElementById('data-series-body');
            tableBody.innerHTML = data.data.map(series => `
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

            renderPagination(data.pagination, (newPage) => loadDataSeries(productId, newPage));
        })
        .catch(error => console.error("DataSeries API Error:", error));
}

// Function to render pagination for data series
function renderPagination(paginationData, updateFunction) {
    console.log("Rendering pagination with data:", paginationData);
    let paginationDiv = document.getElementById('data-series-pagination');
    paginationDiv.innerHTML = '';

    if (paginationData.current_page > 1) {
        let prevButton = document.createElement('button');
        prevButton.textContent = 'Previous';
        prevButton.onclick = () => updateFunction(paginationData.current_page - 1);
        paginationDiv.appendChild(prevButton);
    }

    if (paginationData.current_page < paginationData.last_page) {
        let nextButton = document.createElement('button');
        nextButton.textContent = 'Next';
        nextButton.onclick = () => updateFunction(paginationData.current_page + 1);
        paginationDiv.appendChild(nextButton);
    }
}
