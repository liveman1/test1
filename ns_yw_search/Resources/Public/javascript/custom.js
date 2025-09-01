const arr = [];
const sword = [];
const clickCount = [];
let isExecuting = false;

  addEventListener("DOMContentLoaded", (event) => {
    // Search Modal
    if(document.getElementsByClassName('searchData').length > 0){
      const modalId = TYPO3.settings.TS.searchModalID;      
      for (i = 0; i < document.getElementsByClassName('searchData').length; i++) {
        document.getElementsByClassName('searchData')[i].addEventListener(
            "click",
            (event) => {
              var searchDataPageSlug = document.querySelector(`#modal-${modalId}`).getAttribute('data-slug')
              // var searchDataPageSlug = document.querySelector('.ymSearchData').getAttribute('data-slug')
              if(searchDataPageSlug){
                getUrl = searchDataPageSlug + `?type=990&colpos=0`;
              }else{
                getUrl = '';
              }
              const backDropClass = document.querySelectorAll('.modal-backdrop');
              if(backDropClass){
                  backDropClass.forEach(backdrop => {
                      backdrop.style.zIndex = "-1";
                      backdrop.style.opacity = "0";
                  });
              }
              // Fetch selected page url
              fetch(getUrl, {
                method: 'GET',
              }).then((res) => res.text()).then((res) => {
                // Prpare AJAX Reults
                const parser = new DOMParser();
                const htmlDoc = parser.parseFromString(res, 'text/html');
    
                // const modal = document.querySelector('.ymFavourite');
                const modal = document.querySelector(`#modal-${modalId}`);
                // const modal = document.querySelector('.ymSearchData');
                const modalBody = modal.querySelector('.modal .modal-body');
                const modalHeader = document.querySelector('.modal .modal-header h2');
                
                // Render Page Title to Modalbox
                if(htmlDoc.querySelector('.pageTitle')){
                  modalHeader.innerText = htmlDoc.querySelector('.pageTitle').innerText;
                }
                modalBody.innerHTML = '';
                if(document.getElementById("minimize-filter-search")){
                  document.getElementById("minimize-filter-search").style.display = "inline";
                }
                // Render page content selected on colpos
                if(htmlDoc.querySelector('.pageContent')){
                  modalBody.appendChild(htmlDoc.querySelector('.pageContent'));
                }
                // const myModal = new bootstrap.Modal(document.querySelector('.ymSearchData'));
                customForm = document.querySelector('.pageContent form');
                const formID = customForm.getAttribute('id');
                
                var swiper = new Swiper(".mySwiper", {
                  slidesPerView: 2.2,
                  spaceBetween: 15,
                  pagination: {
                    el: ".swiper-pagination",
                    clickable: true,
                  },
                  breakpoints: {
                    480: {
                      slidesPerView: 4.1,
                    },
                    768: {
                      slidesPerView: 5.1,
                    },
                    993: {
                      slidesPerView: 7.5,
                    }
                  },
                });
    
                customForm.addEventListener(
                  "submit",
                  (event) => {
                    event.preventDefault();
                    const formData = new FormData(customForm); 
    
                    const submitButton = document.querySelector('#form_kesearch_searchfield');
                    // const getUrl = `${entryPoint}/${slug}?type=990&colpos=${colpos}`;
                    // submitButton.action = submitButton.action + `?type=990&colpos=${colpos}`;
                    const currentPageName = submitButton.getAttribute('name');
                    const currentPage = submitButton.getAttribute('value');
                  
                    formData.append(currentPageName,currentPage);
                    const request = new XMLHttpRequest();
                    let action = customForm.getAttribute('action'); 
                    
                    request.open("POST", action, true);
                    
                    request.onreadystatechange = function() {
                      if (request.readyState == XMLHttpRequest.DONE) {
                        const response = request.responseText;
                        const htmlRes = parser.parseFromString(response, 'text/html');
                        
                        const typo3FormResponse =  htmlRes.querySelector('#page-content');
    
                        if (typo3FormResponse) {
                          document.querySelector('.pageContent').innerHTML = '';
                          document.querySelector('.pageContent').appendChild(typo3FormResponse);
                        } else {
                          document.querySelector('.tx-pwcomment-pi1 form').innerHTML = '';
                          const formResponse =  htmlRes.querySelector('.tx-pwcomment-pi1 .typo3-messages');
                          document.querySelector('.pageContent').appendChild(formResponse);
                        }
                        // exit();
                        if(document.getElementById('searchForm')){
                          var i = document.getElementById( 'searchForm' );
                          var d = document.getElementById( 'renderFilter' );
                          d.innerHTML += i.innerHTML;
                          var k = document.getElementsByClassName( 'kesearchbox' )[1];
                          var j = document.getElementById( 'filter' );
                          if(j) {
                            j.style.display = "none";
                          }
                          if(k) {
                            k.style.display = "none";
                          }
                          // k.innerHTML = '';
                        }
                        if(document.getElementById('ke_search_sword')){
                          sword['0'] = document.getElementById('ke_search_sword').value;
                        }
                        callEvent();
                        // selected(null);
                      }
                  };
                
                    request.send(formData);
                    
                  },
                  false
                );
              
                // myModal.show();
    
                // Move Filter to bottom
                if(document.getElementById('searchForm')){
                  var i = document.getElementById( 'searchForm' );
                  var d = document.getElementById( 'renderFilter' );
                  
                  d.innerHTML += i.innerHTML;
                  var k = document.getElementsByClassName( 'kesearchbox' )[1];
                  var j = document.getElementById( 'filter' );
                  if(j) {
                    j.style.display = "none";
                  }
                  if(k) {
                    k.style.display = "none";
                  }
                  // k.innerHTML = '';
                }
              selected(e = []);
              if(arr && sword.length <= 0){
                sword[0] = '';
              }
              if(sword.length > 0){
                document.getElementById("ke_search_sword").setAttribute('value',sword[0]);
                clickCat(e = []); 
              }
              const bodyModal = document.getElementsByClassName('modal-open');
              bodyModal[0].setAttribute('style','overflow: auto !important;');
              var swiper = new Swiper(".mySwiper", {
                slidesPerView: 2.2,
                spaceBetween: 15,
                pagination: {
                  el: ".swiper-pagination",
                  clickable: true,
                },
                breakpoints: {
                  480: {
                    slidesPerView: 4.1,
                  },
                  768: {
                    slidesPerView: 5.1,
                  },
                  993: {
                    slidesPerView: 7.1,
                  }
                },
              });
              
                var results = document.querySelectorAll('.swiper-slide');
                results.forEach(box => {
                  box.addEventListener('click', function handleClick(event) {
                    window.history.pushState('',
                      "", this.dataset.url);
                  });
                });
              });
	      var modal = document.getElementById('modal-587');
              if(modal && clickCount.length < 1){
                modal.addEventListener('click', (e)=> {
                  if(e.target.dataset.cat == 'childCat'){
                      clickCount.push(1);
                      clickCat(e.target);
                  }
                  if(e.target.id == 'closeMe'){
                    const bodyModal = document.getElementsByClassName('modal-open');
                    bodyModal[0].setAttribute('style','overflow: hidden !important;');
                  }
                  if(e.target.id == 'clearSearch'){
                    document.getElementById('ke_search_sword').value = "";
                    document.getElementById("ke_search_sword").setAttribute('value','');
                    sword.length = 0;
    
                    clickCat(e = []);
                  }
                  if(e.target && e.target.id == 'clearFilter'){
                    for(let i = 0; i< arr.length; i++){
                      if(document.getElementsByClassName(arr[i])[0]){
                        document.getElementsByClassName(arr[i])[0].checked = false;
                      }
                      if(document.getElementsByClassName(arr[i])[1]){
                        document.getElementsByClassName(arr[i])[1].checked = false;
                      }
                    }
                    arr.length = 0;
                    clickCat(e = []);
                  }
                });
              }
          }); 
      }
    }
  });
  
function callEvent() {
  customForm = document.querySelector('.pageContent form');
  const formID = customForm.getAttribute('id');
  var swiper = new Swiper(".mySwiper", {
    slidesPerView: 2.2,
    spaceBetween: 15,
    pagination: {
      el: ".swiper-pagination",
      clickable: true,
    },
    breakpoints: {
      480: {
        slidesPerView: 4.1,
      },
      768: {
        slidesPerView: 5.1,
      },
      993: {
        slidesPerView: 7.5,
      }
    },
  });
  customForm.addEventListener(
    "submit",
    (event) => {
      event.preventDefault();
      const formData = new FormData(customForm);
      const parser = new DOMParser();
      const submitButton = document.querySelector('#form_kesearch_searchfield');
      const currentPageName = submitButton.getAttribute('name');
      const currentPage = submitButton.getAttribute('value');
      if(document.getElementById('ke_search_sword')){
        sword['0'] = document.getElementById('ke_search_sword').value;
      }

      formData.append(currentPageName,currentPage);

      const request = new XMLHttpRequest();
      let action = customForm.getAttribute('action');

      request.open("POST", action, true);

      request.onreadystatechange = function() {
        if (request.readyState == XMLHttpRequest.DONE) {
          if (request.status === 200) {
            const response = request.responseText;
            const htmlRes = parser.parseFromString(response, 'text/html');

            const typo3FormResponse =  htmlRes.querySelector('#page-content');

            if (typo3FormResponse) {
              document.querySelector('.pageContent').innerHTML = '';
              document.querySelector('.pageContent').appendChild(typo3FormResponse);
            } else {
              document.querySelector('.tx-pwcomment-pi1 form').innerHTML = '';
              const formResponse =  htmlRes.querySelector('.tx-pwcomment-pi1 .typo3-messages');
              document.querySelector('.pageContent').appendChild(formResponse);
            }
            if(document.getElementById('searchForm')){
              var i = document.getElementById( 'searchForm' );
              var d = document.getElementById( 'renderFilter' );
              d.innerHTML += i.innerHTML;
              var k = document.getElementsByClassName( 'kesearchbox' )[1];
              var j = document.getElementById( 'filter' );
              if(j) {
                j.style.display = "none";
              }
              if(k) {
                k.style.display = "none";
              }
            }
            selected(e = []);
            callEvent();
          }
        }
      };

      request.send(formData);

    },
    false
  );
  var results = document.querySelectorAll('.swiper-slide');
    results.forEach(box => {
      box.addEventListener('click', function handleClick(event) {
        window.history.pushState('',
          "", this.dataset.url);
      });
    });
}


function clickCat(e) {
  customForm = document.querySelector('.pageContent form');
  const formData = new FormData(customForm);
  const parser = new DOMParser();
  const submitButton = document.querySelector('#form_kesearch_searchfield');
  const currentPageName = submitButton.getAttribute('name');
  const currentPage = submitButton.getAttribute('value');
  formData.append(currentPageName, currentPage);
  const request = new XMLHttpRequest();
  let action = customForm.getAttribute('action');

  request.open("POST", action, true);

  request.onreadystatechange = function() {
    if (request.readyState === XMLHttpRequest.DONE) {
      if (request.status === 200) {
        const response = request.responseText;
        const htmlRes = parser.parseFromString(response, 'text/html');
        const typo3FormResponse = htmlRes.querySelector('#page-content');
        if (typo3FormResponse) {
          document.querySelector('.pageContent').innerHTML = '';
          document.querySelector('.pageContent').appendChild(typo3FormResponse);
        }

        if (document.getElementById('searchForm')) {
          var i = document.getElementById('searchForm');
          var d = document.getElementById('renderFilter');
          d.innerHTML += i.innerHTML;
          var k = document.getElementsByClassName('kesearchbox')[1];
          var j = document.getElementById('filter');
          if (j) {
            j.style.display = "none";
          }
          if (k) {
            k.style.display = "none";
          }
        }
        callEvent();
        selected(e);
      } else {
        console.error(`Request failed with status: ${request.status}`);
      }
    }
  };
  // Send the request once
  request.send(formData);
}

function selected(e){
  if(e.id){
    arr.includes(e.id);
    if(arr.includes(e.id)){
      const index = arr.indexOf(e.id);
      if(index >= 0){
          arr.splice(index, 1);
        }
    } else{
      arr.push(e.id);
    }
  }
  for(let i = 0; i< arr.length; i++) {
    if(document.getElementsByClassName(arr[i])[0]){
      document.getElementsByClassName(arr[i])[0].checked = true;
      document.getElementsByClassName(arr[i])[0].parentNode.classList.add("optionCheckBox--active");
    }
    if(document.getElementsByClassName(arr[i])[1]){
      document.getElementsByClassName(arr[i])[1].checked = true;
      document.getElementsByClassName(arr[i])[1].parentNode.classList.add("optionCheckBox--active");
    }
  }
}

addEventListener("DOMContentLoaded", (event) => {
  if(document.getElementById('minimize-filter-search')){
    document.getElementById('minimize-filter-search').addEventListener(
      "click",
      (event) => {
        var x = document.getElementById("searchForm");
        var y = document.getElementById("renderFilter");
        if (x.style.display === "none") {
          x.style.display = "block";
          y.style.display = "block";
        } else {
          x.style.display = "none";
          y.style.display = "none";
        }
      });
  }
});
