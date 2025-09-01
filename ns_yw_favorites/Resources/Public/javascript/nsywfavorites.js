const counter = [];
const selectedCheck = [];
document.documentElement.setAttribute('ontouchmove', 'ontouchmove');
addEventListener("DOMContentLoaded", (event) => {

    if (window.location.href.includes('?listname')) {
        addFavFromCustomUrl();
	    //  console.log('[DEBUG] "?listname" found in URL. Calling addFavFromCustomUrl()...');
    } else {
		    //  console.log('[DEBUG] No "?listname" found in URL. addFavFromCustomUrl() not called.');

    checkLoginForShare();

    // Function to read cookie value
    function getCookie(name) {
        let decodedCookie = decodeURIComponent(document.cookie);
        let ca = decodedCookie.split(';');
        for (let i = 0; i < ca.length; i++) {
            let c = ca[i].trim();
            if (c.indexOf(name + "=") === 0) {
                return c.substring(name.length + 1, c.length);
            }
        }
        return "";
    }

    // Display toast message from cookie on page load
    let toastMessage = getCookie("toastMessage");
    if (toastMessage) {
        tostMe(toastMessage);  // Show toast if cookie exists
        // Clear the cookie after showing the toast
        document.cookie = "toastMessage=; path=/; max-age=0";
    }

    let i;
    const count = document.getElementsByClassName('userList').length;

    if (document.getElementById('minimize-filter')) {
        document.getElementById('minimize-filter').addEventListener(
            "click",
            (event) => {
                var x = document.getElementById('search');
                if (x.style.display === "none") {
                    x.style.display = "block";
                } else {
                    x.style.display = "none";
                }
            });
    }
    var errFlag = document.getElementById('errFlag');
    if (errFlag) {
        if(errFlag.value == 1){
            tostMe(TYPO3.lang.list_not_exists);
            document.getElementById('errFlag').value = 0;
        }
    }
    if (document.getElementsByClassName('addListForm').length > 0) {
        for (i = 0; i < document.getElementsByClassName('addListForm').length; i++) {
            document.getElementsByClassName('addListForm')[i].style.display = 'none';
        }
    }
    if (document.getElementsByClassName('userList').length > 0) {
        addListEvent();
    }

    //Colpos 3 Modal
    if (document.getElementsByClassName('ymPage').length > 0) {
        if (document.getElementsByClassName('fav').length > 0) {
            document.getElementsByClassName('fav')[0].addEventListener(
                "click",
                (event) => {
                    addListEvent();
                });
        }
        if (document.getElementsByClassName('close').length > 0) {
            document.getElementsByClassName('close')[0].addEventListener(
                "click",
                (event) => {
                    // location.reload();
                });
        }

        if (document.getElementsByClassName('favouriteModule').length > 0) {
            for (i = 0; i < document.getElementsByClassName('favouriteModule').length; i++) {
                document.getElementsByClassName('favouriteModule')[i].addEventListener('click', function (e) {
                    
                    let getUrl;

                    var pageSlug = document.querySelector('.ymFavourite').getAttribute('data-slug')
                    if(pageSlug){
                        getUrl = pageSlug + `?type=9000&colpos=0`;
                    }else{
                        getUrl = '';
                    }
                    fetch(getUrl, {
                        method: 'GET',
                    }).then((res) => res.text()).then((res) => {
                        const parser = new DOMParser();
                        const htmlDoc = parser.parseFromString(res, 'text/html');

                        const modalId = TYPO3.settings.TS.favouriteModalID;
                        // const modal = document.querySelector('.ymFavourite');
                        const modal = document.querySelector(`#modal-${modalId}`);
                        const modalBody = modal.querySelector('.ymFavourite .modal-body');
                
                        // if(document.getElementById("btn-minimize")){
                        //     document.getElementById("btn-minimize").style.display = "none";
                        // }
                        // // Render page content selected on colpos
                        if(htmlDoc.querySelector('.fauoritesModalInfo')){
                            modalBody.innerHTML = '';
                            modalBody.innerHTML = htmlDoc.querySelector('.fauoritesModalInfo').innerHTML;
                            // modalBody.replaceChildren(htmlDoc.querySelector('.fauoritesModalInfo'));
                        }

                        counter.splice(0, counter.length);
                        // const elements = document.getElementsByClassName('modal-backdrop');
                        // console.log(elements);
                        // while (elements.length > 0) {
                        //     elements[0].remove();
                        // }
                        const bodyModal = document.getElementsByClassName('modal-open');
                        event.preventDefault();
                        let addListForm = document.getElementById('FavListForm');
                        const formData = new FormData(addListForm);
                        const request = new XMLHttpRequest();
                        let action = addListForm.getAttribute('action');
                        request.open("POST", action, true);
                        request.onreadystatechange = function () {
                            if (request.readyState == XMLHttpRequest.DONE) {

                                const response = request.responseText;
                                const htmlRes = parser.parseFromString(response, 'text/html');
                                if (isJson(response)) {
                                    tostMe(JSON.parse(response)['Error']);
                                } else {
                                    let count = htmlRes.getElementsByClassName('userList').length;
                                    if (htmlRes.getElementsByClassName('userList').length > 1) {
                                        var id = htmlRes.getElementsByClassName('userList')[0].id;
                                    }
                                    if (document.getElementsByClassName('addtofavourite').length > 0) {
                                        document.getElementsByClassName('addtofavourite')[document.getElementsByClassName('addtofavourite').length - 1].innerHTML = '';
                                        document.getElementsByClassName('addtofavourite')[document.getElementsByClassName('addtofavourite').length - 1].appendChild(htmlRes.getElementsByClassName('addToFavMid')[0]);
                                    }
                                    if (document.getElementsByClassName('userList').length > 0) {
                                        // addListEvent();
                                    }
                                    addListEvent();
                                    //get input
                                    let input = document.getElementById("search");
                                    //get list of value
                                    let list = document.querySelectorAll(".userListImage h5");
                                    let desc = document.querySelectorAll(".userListImage p");

                                    //function search on the list.
                                    function search() {
                                        for (let i = 0; i < list.length; i += 1) {
                                            //check if the element contains the value of the input
                                            if (list[i].innerText.toLowerCase().includes(input.value.toLowerCase()) || desc[i].innerText.toLowerCase().includes(input.value.toLowerCase())) {
                                                list[i].closest('.userList').style.display = "block";
                                            } else {
                                                list[i].closest('.userList').style.display = "none";
                                            }
                                        }
                                    }
                                    //to the change run search.
                                    if (input) {
                                        input.addEventListener('input', search);
                                    }
                                }
                                for (var i = 0; i < document.getElementsByClassName('addListForm').length; i++) {
                                    document.getElementsByClassName('addListForm')[i].style.display = 'none';
                                    document.getElementsByClassName('addImg')[0].style.display = "block";
                                }
                            }
                        };
                        request.send(formData);
                    });                  
                });
            }
        }

        for (i = 0; i < document.getElementsByClassName('ymPage').length; i++) {
            document.getElementsByClassName('ymPage')[i].addEventListener(
                "click",
                (event) => {
                    if (document.querySelector('.modal .modal-body').closest('.transparentBg')) {
                       // document.querySelector('.modal .modal-body').closest('.transparentBg').classList.remove('ymSearchData');//ymSearch
                       // document.querySelector('.modal .modal-body').closest('.transparentBg').classList.remove('fade');
                        // document.querySelector('.modal .modal-body').closest('.transparentBg').setAttribute('data-bs-backdrop', 'true');
                       // document.querySelector('.modal .modal-body').closest('.transparentBg').classList.remove('transparentBg');
                    }

                    const backDropClass = document.querySelectorAll('.modal-backdrop');
                    if(backDropClass){
                        backDropClass.forEach(backdrop => {
                            backdrop.style.zIndex = "-1";
                            backdrop.style.opacity = "0";
                        });
                    }
                    var x = event.target;
                    // Let's grab "TYPO3 Slug" for clicked-button
                    const slug = x.getAttribute('id');

                    // Let's grab "TYPO3 colpos" specified in button
                    const colpos = x.getAttribute('menuItem');
	            // Let's grab url with entrypoints                    
		const href = window.location.href;
		const entryPointString = href.split("/").pop();
		const entryPoint = entryPointString.slice(0,5);

                    const modalContent = x.getAttribute('contextModal');
                    if(colpos === '3'){
                        // Prepare AJAX request + Pass to TypoScript with TypeNum & colpos
                        const getUrl = `${entryPoint}/${slug}?type=990&colpos=${colpos}`;
                        fetch(getUrl, {
                            method: 'GET',
                        }).then((res) => res.text()).then((res) => {
                            const parser = new DOMParser();
                            const htmlDoc = parser.parseFromString(res, 'text/html');

                            const modalId = TYPO3.settings.TS.contextModaID;
                            // const modal = document.querySelector(`.${modalContent}`);
                            const modal = document.querySelector(`#modal-${modalId}`);
                            
                            const modalBody = modal.querySelector('.modal-body');
                            const modalHeader = modal.querySelector('.modal-header h2');


                            // const contextModal = new bootstrap.Modal(document.querySelector(`.${modalContent}`));
                            const contextModal = new bootstrap.Modal(document.querySelector(`#modal-${modalId}`));  
                            contextModal.show();                         
                            // Render Page Title to Modalbox
                            if (htmlDoc.querySelector('.pageTitle')) {
                                modalHeader.innerText = htmlDoc.querySelector('.pageTitle').innerText;
                            }
                            modalBody.innerHTML = '';
                            if (document.getElementById("btn-minimize")) {
                                document.getElementById("btn-minimize").style.display = "none";
                            }
                            // Render page content selected on colpos
                            if (htmlDoc.querySelector('.pageContent')) {
                                modalBody.appendChild(htmlDoc.querySelector('.pageContent'));
                            }

                            counter.splice(0, counter.length);

                            if (document.getElementsByClassName('userList').length > 0) {
                                addListEvent();
                            }

                            modalBody.closest('.modal-content').querySelector('.btn-close').addEventListener('click', function (e) {
                                addListEvent();
                                if(document.getElementById('close-btn-addList')) {
                                    document.getElementById('close-btn-addList').click();
                                }
                                if (document.getElementsByClassName('addListForm').length > 0) {
                                    for (let i = 0; i < document.getElementsByClassName('addListForm').length; i++) {
                                        document.getElementsByClassName('addListForm')[i].style.display = 'none';
                                    }
                                }
                            });
                        });
                    }
                });
        }
    }
}
});

function checkLoginForShare()
{
    var urlParams = new URLSearchParams(window.location.search);
    let flag = false;
    let errorMsg = '';
    urlParams.forEach(function(value, key) {
        if(key == 'shareactionres') {
            flag = true;
            errorMsg = value;
        }
    });
    if(flag) {
        if (errorMsg != '') {
            tostMe(errorMsg);
        }
        var searchParams = new URLSearchParams(window.location.search);
        searchParams.delete('shareactionres');
        if(searchParams.toString()) {
            var newURL = window.location.pathname + "?" + searchParams.toString();
        } else {
            var newURL = window.location.pathname;
        }
        history.replaceState(null, null, newURL);
    }
}

//Replace Modal Content 
function showModalContent(modalUrl) {
    fetch(modalUrl, {
        method: 'GET',
    }).then((res) => res.text()).then((res) => {
        const parser = new DOMParser();
        const htmlDoc = parser.parseFromString(res, 'text/html');

        const modal = document.querySelector('.ymFavourite');
        const modalBody = modal.querySelector('.modal .modal-body');

        // if(document.getElementById("btn-minimize")){
        //     document.getElementById("btn-minimize").style.display = "none";
        // }
        // // Render page content selected on colpos
        if(htmlDoc.querySelector('.fauoritesModalInfo')){
            modalBody.replaceChildren(htmlDoc.querySelector('.fauoritesModalInfo'));
        }
    });
}

function addList(event) {
    if (event.target.alt !== 'add') {
        if (event.target.type == 'submit') {
            for (let i = 0; i < document.getElementsByClassName('addListForm').length; i++) {
                document.getElementsByClassName('addListForm')[i].style.display = 'none';
                document.getElementsByClassName('addImg')[i].style.display = "block";
            }
        }
    }
}

function addPageToList(event) {
    // Initialize a counter for outstanding AJAX requests
    //let ajaxRequests = 0;


	var TYPO3 = TYPO3 || {};
	TYPO3.lang = TYPO3.lang || {};
	var currentUrl = window.location.href;
	// Check if the URL contains '/de#'
	if (currentUrl.includes('/de_DE#')) {
		// Code to execute if URL contains '/de#'
		TYPO3.lang.already_added_to = TYPO3.lang.already_added_to || "Bereits hinzugefügt zu"; // Fallback $
		TYPO3.lang.added_to = TYPO3.lang.added_to || "Hinzugefügt zu"; // Fallback value
	} else {
		// Code to execute if URL does not contain '/de#'
		TYPO3.lang.already_added_to = TYPO3.lang.already_added_to || "Already added to"; // Fallback value
		TYPO3.lang.added_to = TYPO3.lang.added_to || "Added to"; // Fallback value
	}
    let i;
    let addPageToList;
    if (document.getElementsByClassName('activeFav').length > 0) {
        for (i = 0; i < document.getElementsByClassName('activeFav').length; i++) {
            document.getElementsByClassName('activeFav')[i].classList.remove('activeFav');
        }
    }
    if (!event.target.closest('.userList').classList.contains('activeFav')) {
        event.target.closest('.userList').classList.add("activeFav");
    }
    const uid = event.target.closest('.userListImage').dataset.uid;
    const reorderInput = document.querySelector('.reorderPage input[name="tx_nsywfavorites_pi2[listId]"]');
    if (reorderInput) {
        reorderInput.value = uid;
    }
    // updateListForm
    if (event.target.alt == 'add' || event.target.alt == 'edit' || event.target.alt == 'del' || event.target.id == 'listname' || event.target.id == 'desc' || event.target.id == 'pic' || event.target.name == "updatefavourite" || event.target.value == "Save" || event.target.value == "Speichern" || event.target.alt == "ibtn" || event.target.alt == 'publicShare' || event.target.alt == 'unsub') {
        if (event.target.alt == 'publicShare') {
            if(document.getElementById('copiedLink')){
                document.getElementById('copiedLink').style.display = "none";
            }
            if (event.target.closest('.userList').querySelector('.userList__unfollow')) {
                if(event.target.closest('.tx-ns-test').querySelector('#editableSpan')){
                    event.target.closest('.tx-ns-test').querySelector('#editableSpan').style.display = 'none';
                }
            } else {
                if(event.target.closest('.tx-ns-test').querySelector('#editableSpan')) {
                    event.target.closest('.tx-ns-test').querySelector('#editableSpan').style.display = 'block';
                }
            }
            addPageToList = event.target.closest('.userList').querySelector('.sliderForm');
            const formData = new FormData(addPageToList);
            const request = new XMLHttpRequest();
            let action = addPageToList.getAttribute('action');
            request.open("POST", action, true);
            const parser = new DOMParser();

            request.onreadystatechange = function () {
                if (request.readyState == XMLHttpRequest.DONE) {

                    const response = request.responseText;
                    const htmlRes = parser.parseFromString(response, 'text/html');
                    i = 0;
                    if (event.target.closest('.userList').querySelector('.updateList')) {
                        event.target.closest('.userList').querySelector('.updateList').style.display = "none";
                    }
                    // let count = htmlRes.getElementsByClassName('userList').length;
                    document.getElementById('sliderData').innerHTML = '';
                    document.getElementById('sliderData').appendChild(htmlRes.getElementById('sliderData'));
                    // addEventToLastAdded('userList');
                    var swiper = new Swiper(".mySwiper", {
                        slidesPerView: 2.2,
                        spaceBetween: 15,
                        pagination: {
                            el: ".swiper-pagination",
                            clickable: true,
                        },
                        breakpoints: {
                            480: {
                                slidesPerView: 2,
                            },
                            768: {
                                slidesPerView: 3,
                            },
                            993: {
                                slidesPerView: 7.5,
                            }
                        },
                    });


                    for (var j = 0; j < document.getElementsByClassName('userList__delete__page').length; j++) {
                        var clone = document.getElementsByClassName('userList__delete__page')[j].cloneNode(true);
                        document.getElementsByClassName('userList__delete__page')[j].addEventListener(
                            "click",
                            (event) => {
                                deletePage(event);
                            });
                        document.getElementsByClassName('userList__left__page')[j].addEventListener(
                            "click",
                            (event) => {
                                leftPage(event);
                            });
                    document.getElementsByClassName('userList__right__page')[j].addEventListener(
                        "click",
                        (event) => {
                            rightPage(event);
                        });
                }
                bindReorderButton();
                }
            }
            request.send(formData);
            const uid = event.target.closest('.userListImage').dataset.uid;
            if(document.getElementById('shareOptions')){
                document.getElementById('shareOptions').style.display = 'block';
            }
            if (event.target.closest('.tx-ns-test')) {
                event.target.closest('.tx-ns-test').querySelector('#sliderData').style.display = "block";
            }
            if(document.getElementById('copyLink')){
                document.getElementById('copyLink').style.display = 'none';
            }

            let index = selectedCheck.indexOf(uid);
            if (index > -1 || event.target.closest('.userList').querySelector('#checkflag').value == 1) { // only splice array when item is found
                if(document.querySelector('#editable')){
                    document.querySelector('#editable').checked = true;
                }
            } else {
                if(document.querySelector('#editable')){
                    document.querySelector('#editable').checked = false;
                }
            }

            const element = document.getElementById('editable');
            if(element){
                const clonedElement = element.cloneNode(true);
                element.parentNode.replaceChild(clonedElement, element);
            }
            if(element){
                document.getElementById('editable').addEventListener(
                    "change",
                    (event) => {
                        var linkForm = event.target.closest('#shareOptions').querySelector('.duplicateList');
                        var url = linkForm.action + '&tx_nsywfavorites_pi2[uid]=' + uid;
                        url = url.replace(/[\?&]cHash=[^&]+/, '');
                        if (!event.target.closest('#shareOptions').querySelector('#editable').checked) {
                            let index = selectedCheck.indexOf(uid);
                            if (index > -1) { // only splice array when item is found
                                selectedCheck.splice(index, 1); // 2nd parameter means remove one item only
                            }
                            url = url + '&tx_nsywfavorites_pi2[editable]=0&tx_nsywfavorites_pi2[change]=11';
                        } else {
                            selectedCheck.push(uid);
    
                            url = url + '&tx_nsywfavorites_pi2[editable]=1&tx_nsywfavorites_pi2[change]=10';
                        }
                        if (url) {
                            var addurl = event.target.closest('#shareOptions').querySelector('.crypticurl');
                            const formData = new FormData(addurl);
                            formData.append('url', url);
                            const request = new XMLHttpRequest();
                            let action = addurl.getAttribute('action');
                            request.open("POST", action, true);
                            const parser = new DOMParser();
                            request.onreadystatechange = function () {
                                if (request.readyState == XMLHttpRequest.DONE) {

                                    const response = request.responseText;
                                    const htmlRes = parser.parseFromString(response, 'text/html');
                                }
                            }
                            request.send(formData);
                        }
                    });
            }

            if(document.querySelector('#generatedLink')){
                document.querySelector('#generatedLink').innerHTML = 'Link Generating...';
            }

            if(document.getElementById('shareOptions')){
                var linkForm = document.getElementById('shareOptions').querySelector('.duplicateList');
                var url = linkForm.action + '&tx_nsywfavorites_pi2[uid]=' + uid;
                url = url.replace(/[\?&]cHash=[^&]+/, '');
            }
            if(document.getElementById('shareOptions')){
                if (!document.getElementById('shareOptions').querySelector('#editable').checked) {
                    url = url + '&tx_nsywfavorites_pi2[editable]=0';
                } else {
                    url = url + '&tx_nsywfavorites_pi2[editable]=1';
                }
            }
            if (url) {
                var addurl = document.getElementById('shareOptions').querySelector('.crypticurl');
                const formData = new FormData(addurl);
                formData.append('url', url);
                const request = new XMLHttpRequest();
                let action = addurl.getAttribute('action');
                request.open("POST", action, true);
                const parser = new DOMParser();
                request.onreadystatechange = function () {
                    if (request.readyState == XMLHttpRequest.DONE) {

                        const response = request.responseText;
                        const htmlRes = parser.parseFromString(response, 'text/html');
                        document.getElementById('shareSpan').querySelector('#generatedLink').innerHTML = '';
                        document.getElementById('shareSpan').querySelector('#generatedLink').innerHTML = response;
                        document.getElementById('copyLink').style.display = 'block';
                        document.getElementById('shareSpan').querySelector('#generatedLink').style.display = 'none';
                    }
                }
                request.send(formData);
            }

            if(document.getElementById('copyLink')){
                document.getElementById('copyLink').addEventListener(
                    "click",
                    (event) => {
                        document.getElementById('shareSpan').querySelector('#generatedLink').style.display = ''
                        document.getElementById('copyLink').style.display = 'none';
                        document.getElementById('copiedLink').style.display = 'block';
                        var element = document.getElementById('generatedLink');
                        // Select the text in the element
                        var range = document.createRange();
                        range.selectNodeContents(element);
    
                        // Create a new selection with the text
                        var selection = window.getSelection();
                        selection.removeAllRanges();
                        selection.addRange(range);
    
                        // Copy the selected text to the clipboard
                        document.execCommand('copy');
    
                        selection.removeAllRanges();
                        // tostMe('Link Copied!');
                        document.getElementById('shareSpan').querySelector('#generatedLink').style.display = 'none'
                    });
            }
        }
        if (event.target.alt == 'unsub') {
            let text = "Are you sure to delete List ?";
            if (confirm(text) == true) {
                var deleteList = event.target.closest('.userList').querySelector('.unfollow');
                event.preventDefault();
                const formData = new FormData(deleteList);
                const request = new XMLHttpRequest();
                let action = deleteList.getAttribute('action');
                request.open("POST", action, true);
                const parser = new DOMParser();
                request.onreadystatechange = function () {
                    if (request.readyState == XMLHttpRequest.DONE) {

                        const response = request.responseText;
                        event.target.closest('.userList').remove();
                        tostMe(TYPO3.lang.list_delete_success);
                    }
                };
                request.send(formData);
                if (event.target.closest('.tx-ns-test').querySelector('#sliderData')) {
                    event.target.closest('.tx-ns-test').querySelector('#sliderData').style.display = "none";
                }
                if (event.target.closest('.tx-ns-test').querySelector('#shareOptions')) {
                    event.target.closest('.tx-ns-test').querySelector('#shareOptions').style.display = "none";
                }
            } else {
                event.preventDefault();
            }
        }
    } else {
        if (event.target.closest('.userList').querySelector('.userList__edit')) {
            if(event.target.closest('.userList').querySelector('.addPageToList')){
                addPageToList = event.target.closest('.userList').querySelector('.addPageToList');
                event.preventDefault();
    var spinner = document.createElement('div');
    spinner.style.width = '40px'; // Adjust size as needed
    spinner.style.height = '40px';
    spinner.style.border = '6px solid rgba(0, 0, 0, 0.1)';
    spinner.style.borderTopColor = '#333';
    spinner.style.borderRadius = '50%';
    spinner.style.animation = 'spin 1s linear infinite';
    spinner.style.position = 'absolute';
    spinner.style.zIndex = '1000';

    // Position the spinner over the clicked element
    var clickedElementRect = event.target.getBoundingClientRect();
    var scrollTop = window.pageYOffset || document.documentElement.scrollTop;
    var scrollLeft = window.pageXOffset || document.documentElement.scrollLeft;

    // Adjust spinner position (centered over the clicked element)
    spinner.style.left = (clickedElementRect.left + scrollLeft + event.target.offsetWidth / 2 - 20) + 'px';
    spinner.style.top = (clickedElementRect.top + scrollTop + event.target.offsetHeight / 2 - 20) + 'px';
	    // Append the spinner to the body
	    document.body.appendChild(spinner);
                const formData = new FormData(addPageToList);
                const request = new XMLHttpRequest();
                let action = addPageToList.getAttribute('action');
                request.open("POST", action, true);
                const parser = new DOMParser();
                request.onreadystatechange = function () {
                    if (request.readyState == XMLHttpRequest.DONE) {
			// Remove the spinner
			document.body.removeChild(spinner);
                        const response = request.responseText;
                        i = 0;
                        if (isJson(response)) {
                            if (JSON.parse(response)['Err']) {
                                tostMe(TYPO3.lang.already_added_to + ' ' + JSON.parse(response)['Err']);
                            }
                            if (JSON.parse(response)['succ']) {
                                document.getElementsByClassName('addToFavMid')[0].insertBefore(event.target.closest('.userList'), document.getElementsByClassName('userList')[0]);
                                tostMe(TYPO3.lang.added_to + ' ' + JSON.parse(response)['succ']);
                            }
                        }
                    }
                };
                request.send(formData);
            }
        }
    }
}
function isJson(str) {
    try {
        JSON.parse(str);
    } catch (e) {
        return false;
    }
    return true;
}
function tostMe(msg) {
    var options = {
        text: msg,
        duration: 3000,
        position: 'left',
        callback: function () {
            Toastify.reposition();
        },
        close: true,
        style: {
            background: "linear-gradient(to right, #00b09b, #96c93d)",
        }
    };
    // Initializing the toast
    var myToast = Toastify(options);
    myToast.showToast();
}
function addListEvent() {
    if (document.getElementsByClassName('addList').length > 0 && counter.length < 10) {
        for (let j = 0; j < document.getElementsByClassName('addList').length; j++) {
            const clone = document.getElementsByClassName('addList')[j].cloneNode(true);
            document.getElementsByClassName('addList')[j].addEventListener(
                "click",
                (event) => {
                    addList(event);
                });
        }
    }
}
function duplicateById(className) {
    var elms = document.querySelectorAll("[id='" + className + "']");
    for (var i = 1; i < elms.length; i++) {
        elms[i].closest('.userList').addEventListener(
            "click",
            (event) => {
                addPageToList(event);
            });
    }
}
function deletePage(event) {
    let text = "Are you sure to delete List ?";
    var removePage = event.target.closest('.userList__delete__page').querySelector('.deletePage');
    const formData = new FormData(removePage);
    const request = new XMLHttpRequest();
    let action = removePage.getAttribute('action');
    request.open("POST", action, true);
    const parser = new DOMParser();
    request.onreadystatechange = function () {
        if (request.readyState == XMLHttpRequest.DONE) {
            const response = request.responseText;
            event.target.closest('.swiper-slide').remove();
            tostMe(TYPO3.lang.page_remove_from_list);
        }
    };
    request.send(formData);

}
function leftPage(event) {
    var removePage = event.target.closest('.userList__left__page').querySelector('.leftPage');
    const formData = new FormData(removePage);
    const request = new XMLHttpRequest();
    let action = removePage.getAttribute('action');
    request.open("POST", action, true);
    const parser = new DOMParser();
    request.onreadystatechange = function () {
        if (request.readyState == XMLHttpRequest.DONE) {
            const response = request.responseText;
            const htmlRes = parser.parseFromString(response, 'text/html');
            if (isJson(response)) {
                tostMe(JSON.parse(response)['Error']);
            } else {
                i = 0;
                // let count = htmlRes.getElementsByClassName('userList').length;
                document.getElementById('sliderData').innerHTML = '';
                document.getElementById('sliderData').appendChild(htmlRes.getElementById('sliderData'));

                // addEventToLastAdded('userList');
                var swiper = new Swiper(".mySwiper", {
                    slidesPerView: 2.2,
                    spaceBetween: 15,
                    pagination: {
                        el: ".swiper-pagination",
                        clickable: true,
                    },
                    breakpoints: {
                        480: {
                            slidesPerView: 2,
                        },
                        768: {
                            slidesPerView: 3,
                        },
                        993: {
                            slidesPerView: 7.5,
                        }
                    },
                });
                for (var j = 0; j < document.getElementsByClassName('userList__delete__page').length; j++) {
                    var clone = document.getElementsByClassName('userList__delete__page')[j].cloneNode(true);
                    document.getElementsByClassName('userList__delete__page')[j].addEventListener(
                        "click",
                        (event) => {
                            deletePage(event);
                        });
                    document.getElementsByClassName('userList__left__page')[j].addEventListener(
                        "click",
                        (event) => {
                            leftPage(event);
                        });
                    document.getElementsByClassName('userList__right__page')[j].addEventListener(
                        "click",
                        (event) => {
                            rightPage(event);
                        });
                }
                bindReorderButton();
            }
        }
    };
    request.send(formData);

}
function rightPage(event) {
    var removePage = event.target.closest('.userList__right__page').querySelector('.rightPage');
    const formData = new FormData(removePage);
    const request = new XMLHttpRequest();
    let action = removePage.getAttribute('action');
    request.open("POST", action, true);
    const parser = new DOMParser();
    request.onreadystatechange = function () {
        if (request.readyState == XMLHttpRequest.DONE) {
            const response = request.responseText;
            const htmlRes = parser.parseFromString(response, 'text/html');
            if (isJson(response)) {
                tostMe(JSON.parse(response)['Error']);
            } else {

                i = 0;
                // let count = htmlRes.getElementsByClassName('userList').length;
                document.getElementById('sliderData').innerHTML = '';
                document.getElementById('sliderData').appendChild(htmlRes.getElementById('sliderData'));

                // addEventToLastAdded('userList');
                var swiper = new Swiper(".mySwiper", {
                    slidesPerView: 2.2,
                    spaceBetween: 15,
                    pagination: {
                        el: ".swiper-pagination",
                        clickable: true,
                    },
                    breakpoints: {
                        480: {
                            slidesPerView: 2,
                        },
                        768: {
                            slidesPerView: 3,
                        },
                        993: {
                            slidesPerView: 7.5,
                        }
                    },
                });
                for (var j = 0; j < document.getElementsByClassName('userList__delete__page').length; j++) {
                    var clone = document.getElementsByClassName('userList__delete__page')[j].cloneNode(true);
                    document.getElementsByClassName('userList__delete__page')[j].addEventListener(
                        "click",
                        (event) => {
                            deletePage(event);
                        });
                    document.getElementsByClassName('userList__left__page')[j].addEventListener(
                        "click",
                        (event) => {
                            leftPage(event);
                        });
                    document.getElementsByClassName('userList__right__page')[j].addEventListener(
                        "click",
                        (event) => {
                            rightPage(event);
                        });
                }
                bindReorderButton();
            }
        }
    };
    request.send(formData);

}

function reorderPage(event) {
    event.preventDefault();
    var reorderForm = event.target.closest('.reorderPage');
    const formData = new FormData(reorderForm);
    const request = new XMLHttpRequest();
    let action = reorderForm.getAttribute('action');
    request.open("POST", action, true);
    const parser = new DOMParser();
    request.onreadystatechange = function () {
        if (request.readyState == XMLHttpRequest.DONE) {
            const response = request.responseText;
            const htmlRes = parser.parseFromString(response, 'text/html');
            if (!isJson(response)) {
                document.getElementById('sliderData').innerHTML = '';
                document.getElementById('sliderData').appendChild(htmlRes.getElementById('sliderData'));
                var swiper = new Swiper(".mySwiper", {
                    slidesPerView: 2.2,
                    spaceBetween: 15,
                    pagination: {
                        el: ".swiper-pagination",
                        clickable: true,
                    },
                    breakpoints: {
                        480: { slidesPerView: 2 },
                        768: { slidesPerView: 3 },
                        993: { slidesPerView: 7.5 }
                    },
                });
                for (var j = 0; j < document.getElementsByClassName('userList__delete__page').length; j++) {
                    document.getElementsByClassName('userList__delete__page')[j].addEventListener(
                        "click",
                        (event) => {
                            deletePage(event);
                        }
                    );
                    document.getElementsByClassName('userList__left__page')[j].addEventListener(
                        "click",
                        (event) => {
                            leftPage(event);
                        }
                    );
                    document.getElementsByClassName('userList__right__page')[j].addEventListener(
                        "click",
                        (event) => {
                            rightPage(event);
                        }
                    );
                }
                bindReorderButton();
                tostMe(TYPO3.lang.list_reordered_success || 'Reordered!');
            }
        }
    };
    request.send(formData);
}

function bindReorderButton() {
    var reorderBtn = document.querySelector('.userList__reorder__page');
    if (reorderBtn) {
        const btnClone = reorderBtn.cloneNode(true);
        reorderBtn.parentNode.replaceChild(btnClone, reorderBtn);
        btnClone.addEventListener('click', reorderPage);
    }
}

//Updated code for the event handling...
document.addEventListener("DOMContentLoaded", function() {

// Function to handle clicks within the modal
function handleModalClick(event) {
	if (event.target.classList.contains('favouriteModule') || (event.target.tagName === 'IMG' && !event.target.className.includes('addImg'))) {
		switch (event.target.alt) {
			case 'add':
				addListButton(event);
				break;
			case 'edit':
				editListButton(event);
				break;
			case 'del':
				deleteList(event);
				break;
		}
	}

	const closeElement = event.target.closest('.userList');
	if (closeElement !== null) {
		if (event.target.classList.contains('favouriteModule') || (event.target.alt != 'edit' && event.target.alt != 'del' && event.target.alt != 'add')) {
			addPageToList(event);
		}
	}

	const addPlusButton = event.target.closest('.addList');
	if (addPlusButton !== null) {
		if (event.target.classList.contains('favouriteModule') || event.target.className.includes('addImg')) {
			addListPlusButton(event);
		}
	}
}

// Select the specific modals by ID
const modal589 = document.getElementById('modal-589');
const modal436 = document.getElementById('modal-436');

// Check if the modals exist and attach the event listener
if (modal589) {
	modal589.addEventListener('click', handleModalClick);
}
if (modal436) {
        modal436.addEventListener('click', handleModalClick);
}

    bindReorderButton();

});

function editListButton(event) {
    event.target.closest('.userList').querySelector('.updateList').style.display = "block";
    event.target.closest('.userList').querySelector('.userListImage').style.display = "none";
    event.target.closest('.userList').classList.add("mystyle");
    if (event.target.closest('.userList').querySelector('.userList__delete')) {
        event.target.closest('.userList').querySelector('.userList__delete').style.display = "none";
    }
    if (event.target.closest('.userList').querySelector('.userList__unfollow')) {
        event.target.closest('.userList').querySelector('.userList__unfollow').style.display = "none";
    }
    event.target.closest('.userList').querySelector('.userList__edit').style.display = "none";
    if(event.target.closest('.userList').querySelector('.userList__add')){
        event.target.closest('.userList').querySelector('.userList__add').style.display = "none";
    }
    if (event.target.closest('.userList').querySelector('.userList__i')) {
        event.target.closest('.userList').querySelector('.userList__i').style.display = "none";
    }


    const updateListForm = event.target.closest('.userList').querySelector('.updateListForm');
    updateListForm.style.display = "block";
    event.preventDefault();
    updateListForm.addEventListener("submit", (event) => {
        event.preventDefault();
        const formData = new FormData(updateListForm);
        const request = new XMLHttpRequest();
        let action = updateListForm.getAttribute('action');
        request.open("POST", action, true);
        const parser = new DOMParser();
        request.onreadystatechange = function () {
            if (request.readyState == XMLHttpRequest.DONE) {
                const response = request.responseText;
                const htmlRes = parser.parseFromString(response, 'text/html');
                if (isJson(response)) {
                    tostMe(JSON.parse(response)['Error']);
                } else {
                    const id = event.target.closest('.userList').id;
                    let firstListId = event.target.closest('.addToFavMid').querySelector('.userList').id;
                    event.target.closest('.addToFavMid').insertBefore(htmlRes.getElementById(event.target.closest('.userList').id), event.target.closest('.addToFavMid').querySelector('#' + firstListId));
                    event.target.closest('.userList').querySelector('.updateList').remove();
                }
            }
        };
        request.send(formData);
    });
}
function addListButton(event) {
    var addPageToList = event.target.closest('.userList').querySelector('.duplicateList');
    const formData = new FormData(addPageToList);
    const request = new XMLHttpRequest();
    let action = addPageToList.getAttribute('action');
    request.open("POST", action, true);
    const parser = new DOMParser();
    request.onreadystatechange = function () {
        if (request.readyState == XMLHttpRequest.DONE) {
            const response = request.responseText;
            const htmlRes = parser.parseFromString(response, 'text/html');
            i = 0;
            let arr = [];
            let arr1 = [];
            if (document.getElementsByClassName('duplicateList').length > 0) {
                for (var j = 0; j < document.getElementsByClassName('duplicateList').length; j++) {
                    arr.push(document.getElementsByClassName('duplicateList')[j].getAttribute('id'));
                }
                for (var k = 0; k < htmlRes.getElementsByClassName('duplicateList').length; k++) {
                    arr1.push(htmlRes.getElementsByClassName('duplicateList')[k].getAttribute('id'));
                }
            }
            let difference = arr1.filter(x => !arr.includes(x));
            if (difference) {
                var id = difference[0];
                let firstListId = event.target.closest('.addToFavMid').querySelector('.userList').id;
                event.target.closest('.addToFavMid').insertBefore(htmlRes.getElementById(id).closest('.userList'), event.target.closest('.addToFavMid').querySelector('#' + firstListId));
                if (event.target.closest('.userList').querySelector('.updateList')) {
                    event.target.closest('.userList').querySelector('.updateList').style.display = "none";
                }
                let listId = 'userList-'+getSecondPart(id);
                let userList = document.getElementById(listId).querySelector('h5').innerText;
                tostMe(TYPO3.lang.added_to + ' ' + userList);
            }
        }
    }
    request.send(formData);
}
function deleteList(event) {
    let text = "Are you sure to delete List ?";
    if (confirm(text) == true) {
        event.preventDefault();
        const request = new XMLHttpRequest();
        let action = event.target.getAttribute('data-href');
        request.open("POST", action, true);
        const parser = new DOMParser();
        request.onreadystatechange = function () {
            if (request.readyState == XMLHttpRequest.DONE) {
                const response = request.responseText;
                event.target.closest('.userList').remove();
                tostMe(TYPO3.lang.list_delete_success);
            }
        };
        request.send();
        if (event.target.closest('.tx-ns-test').querySelector('#sliderData')) {
            event.target.closest('.tx-ns-test').querySelector('#sliderData').style.display = "none";
        }
        if (event.target.closest('.tx-ns-test').querySelector('#shareOptions')) {
            event.target.closest('.tx-ns-test').querySelector('#shareOptions').style.display = "none";
        }
    }
}
function addListPlusButton(event) {
    event.target.style.display = "none";
    if (document.getElementsByClassName('addListForm').length > 0) {
        for (var i = 0; i < document.getElementsByClassName('addListForm').length; i++) {
            document.getElementsByClassName('addListForm')[i].style.display = 'block';
        }
        // close-btn-addList
        event.target.closest('.addToFavMid').querySelector('#close-btn-addList').addEventListener("click",function(){
            event.target.style.display = "block";
            event.target.closest('.addToFavMid').querySelector('.addListForm').style.display = "none";
        });

        var addListForm = document.querySelector('#addListForm');
        var addFavListForm = document.querySelector('#addFavListForm');
        let val = document.getElementById('listname').value;
        let desc = document.getElementById('desc').value;
        if (event.target.closest('.addList').querySelector('#addListForm')) {
            addListForm.addEventListener(
                "submit",
                (event) => {
                    event.preventDefault();
                    const formData = new FormData(addListForm);
                    const request = new XMLHttpRequest();
                    let action = addListForm.getAttribute('action');
                    request.open("POST", action, true);
                    const parser = new DOMParser();
                    request.onreadystatechange = function () {
                        if (request.readyState == XMLHttpRequest.DONE) {
                            const response = request.responseText;
                            const htmlRes = parser.parseFromString(response, 'text/html');
                            if (isJson(response)) {
                                tostMe(JSON.parse(response)['Error']);
                            } else {
                                const id = htmlRes.getElementsByClassName('userList')[0].id;
                                document.getElementsByClassName('addToFavMid')[0].insertBefore(
                                    htmlRes.getElementsByClassName('userList')[0],
                                    document.getElementsByClassName('userList')[0]
                                );
                                document.getElementById(id).click();
                                document.getElementById('pic').value = '';
                                document.getElementById('listname').value = '';
                                document.getElementById('listname').value = val;
                                document.getElementById('desc').value = '';
                                document.getElementById('desc').value = desc;
                            }
                            for (let i = 0; i < document.getElementsByClassName('addListForm').length; i++) {
                                document.getElementsByClassName('addListForm')[i].style.display = 'none';
                                document.getElementsByClassName('addImg')[0].style.display = "block";
                            }
                        }
                    };
                    request.send(formData);
                },{once: true});
        }
        if (event.target.closest('.addList').querySelector('#addFavListForm')) {
            addFavListForm.addEventListener(
                "submit",
                (event) => {
                    event.preventDefault();
                    const formData = new FormData(addFavListForm);
                    const request = new XMLHttpRequest();
                    let action = addFavListForm.getAttribute('action');
                    request.open("POST", action, true);
                    const parser = new DOMParser();
                    request.onreadystatechange = function () {
                        if (request.readyState == XMLHttpRequest.DONE) {
                            const response = request.responseText;
                            const htmlRes = parser.parseFromString(response, 'text/html');
                            if (isJson(response)) {
                                tostMe(JSON.parse(response)['Error']);
                            } else {
                                let count = htmlRes.getElementsByClassName('userList').length;
                                const id = htmlRes.getElementsByClassName('userList')[0].id;
                                // document.getElementsByClassName('addToFavMid')[document.getElementsByClassName('addToFavMid').length - 1]
                                if (document.getElementsByClassName('userList').length > 0) {
                                    let firstListId = event.target.closest('.addToFavMid').querySelector('.userList').id;
                                    event.target.closest('.addToFavMid').insertBefore(htmlRes.getElementsByClassName('userList')[0], event.target.closest('.addToFavMid').querySelector('#' + firstListId));
                                } else {
                                    // document.getElementsByClassName('addToFavMid')[0].innerHTML = htmlRes.getElementsByClassName('userList')[0];
                                    document.getElementsByClassName('addToFavMid')[0].appendChild(htmlRes.getElementsByClassName('userList')[0]);
                                }
                                document.getElementById(id).click();
                                event.target.querySelector('#pic').value = '';
                                event.target.querySelector('#listname').value = '';
                                event.target.querySelector('#desc').value = '';
                            }
                            for (let i = 0; i < document.getElementsByClassName('addListForm').length; i++) {
                                document.getElementsByClassName('addListForm')[i].style.display = 'none';
                                document.getElementsByClassName('addImg')[0].style.display = "block";
                            }
                            //get input
                            let input = document.getElementById("search");
                            //get list of value
                            let list = document.querySelectorAll(".userListImage h5");
                            let desc = document.querySelectorAll(".userListImage p");

                            //function search on the list.
                            function search() {
                                for (let i = 0; i < list.length; i += 1) {
                                    //check if the element contains the value of the input
                                    if (list[i].innerText.toLowerCase().includes(input.value.toLowerCase()) || desc[i].innerText.toLowerCase().includes(input.value.toLowerCase())) {
                                        list[i].closest('.userList').style.display = "block";
                                    } else {
                                        list[i].closest('.userList').style.display = "none";
                                    }
                                }
                            }

                            //to the change run search.
                            if (input) {
                                input.addEventListener('input', search);
                            }
                        }
                    };
                    request.send(formData);
                },
                {once: true}
            );
        }
    }
}

function getSecondPart(str) {
    return str.split('-')[1];
}
