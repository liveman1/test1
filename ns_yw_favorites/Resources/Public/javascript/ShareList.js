document.addEventListener('DOMContentLoaded', () => {
    linkGeneration();
    const filterForm = document.querySelector('#filterByFeUser');
    if (filterForm) {
        const filterDropdown = filterForm.querySelector('#userFilterDropdown');

        filterDropdown.addEventListener('change', () => {
            const mainDataDiv = document.querySelector('#main-data');
            const mainLoader = document.querySelector('.main-loader');
            mainLoader.classList.remove('d-none');
            mainDataDiv.classList.add('d-none');
            let formData = new FormData();
            formData.append('selectedOption', filterDropdown.value);
            let request = new XMLHttpRequest();
            let action = filterForm.getAttribute('action');
            request.open("POST", action, true);
            request.onreadystatechange = function () {
                if (request.readyState === XMLHttpRequest.DONE) {
                    const response = request.responseText;
                    const parser = new DOMParser();
                    const htmlRes = parser.parseFromString(response, 'text/html');
                    mainDataDiv.innerHTML = htmlRes.querySelector('#main-data').innerHTML;
                    linkGeneration();
                    mainLoader.classList.add('d-none');
                    mainDataDiv.classList.remove('d-none');
                }
            };
            request.send(formData);
        });
    }

    function linkGeneration() {
        const shareActionUrl = document.querySelector('#shareLinkGenerate');
        const urlGenerateText = document.querySelectorAll('.share-list-url-generation');
        const encryptUrlForm = document.querySelector('#encryptUrl');
        const addToFavSlug = document.querySelector('#addToFavSlug');
        const favPageSlug = document.querySelector('#favPageSlug');

        if (urlGenerateText) {
            urlGenerateText.forEach((item) => {
                item.addEventListener('click', (e) => {
                    let listUid = item.getAttribute('data-id');
                    let actionUrl = shareActionUrl.action.replace('tx_nsywfavorites_pi3', 'tx_nsywfavorites_pi2');
                    let url = actionUrl + '&tx_nsywfavorites_pi2[uid]=' + listUid;
                    url = url.replace(/[\?&]cHash=[^&]+/, '');
                    let isEditable = item.parentNode.querySelector('.isEditable');
                    if (isEditable.checked) {
                        url = url + '&tx_nsywfavorites_pi2[editable]=1';
                    } else {
                        url = url + '&tx_nsywfavorites_pi2[editable]=0';
                    }
                    url = url.replace('tx_nsywfavorites_pi3', 'tx_nsywfavorites_pi2');
                    let pathname = window.location.pathname;
                    let pageName = pathname.split("/").pop();
                    let entryPoint = TYPO3.settings.ENTRY_POINT;
                    if (pathname !== entryPoint) {
                        url = url.replace('/' + pageName, favPageSlug.value);
                    } else {
                        url = url.replace(entryPoint, entryPoint + favPageSlug.value);
                    }
                    const loader = item.parentNode.parentNode.querySelector('.loading-spinner');
                    const dataDiv = item.parentNode.parentNode.querySelector('.url-data');
                    loader.classList.remove('d-none');
                    dataDiv.classList.add('d-none');
                    const formData = new FormData(encryptUrlForm);
                    formData.append('url', url);
                    formData.append('addToFavSlug', addToFavSlug.value);
                    formData.append('favPageSlug', favPageSlug.value);
                    const request = new XMLHttpRequest();
                    let action = encryptUrlForm.getAttribute('action');
                    request.open("POST", action, true);
                    request.onreadystatechange = function () {
                        if (request.readyState === XMLHttpRequest.DONE) {
                            const response = request.responseText;
                            copyToClipboard(response);
                            loader.classList.add('d-none');
                            dataDiv.classList.remove('d-none');
                        } else {
                            loader.classList.add('d-none');
                            dataDiv.classList.remove('d-none');
                        }
                    };
                    request.send(formData);
                });
            });
        }
    }
});

function copyToClipboard(text) {
    navigator.clipboard.writeText(text)
        .then(() => {
            tostMe('URL copied to clipboard successfully.');
        })
        .catch(err => {
            tostMe('Something went wrong!, Check the console for more details.');
            console.error('Could not copy text to clipboard: ', err);
        });
}

