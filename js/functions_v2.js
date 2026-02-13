document.addEventListener('DOMContentLoaded', () => {

  /*--------------------------------------------------------------
    Menu pantalla de escritorio
  ----------------------------------------------------------------*/

  // Inserta los dropdowns creados a los items del menú primer nivel
  document.querySelectorAll('.dropdown-menu').forEach((_, idx) => {
    const i = idx + 1;
    const dropdown = document.querySelector('.dropdown-menu-' + i);
    const mainMenuItem = document.querySelector('.first-level-' + i + ' > a');
    if (dropdown && mainMenuItem && mainMenuItem.parentNode) {
      mainMenuItem.parentNode.insertBefore(dropdown, mainMenuItem.nextSibling);
    }
  });

  // Behavior depending on whether we're on home
  const bodyIsHome = document.body.classList.contains('home');
  const mainBar = document.getElementById('main-bar');
  const logoImg = document.querySelector('.et_pb_menu__logo img');

  if (mainBar) {
    mainBar.addEventListener('mouseenter', () => {
      if (logoImg) logoImg.src = '/wp-content/uploads/2024/09/logo-blanco-cimne-web.png';
      mainBar.classList.add('hover');
    });
    mainBar.addEventListener('mouseleave', () => {
      if (logoImg) logoImg.src = '/wp-content/uploads/2024/09/logo-color-cimne-web.png';
      mainBar.classList.remove('hover');
    });
  }

  document.querySelectorAll('li.first-level').forEach(li => {
    li.addEventListener('mouseenter', () => {
      if (!bodyIsHome) {
        if (logoImg) logoImg.src = '/wp-content/uploads/2024/09/logo-blanco-cimne-web.png';
        if (mainBar) mainBar.classList.add('hover');
      } else {
        if (mainBar) mainBar.style.backgroundColor = '#004996';
      }
    });
    li.addEventListener('mouseleave', () => {
      if (!bodyIsHome) {
        if (logoImg) logoImg.src = '/wp-content/uploads/2024/09/logo-color-cimne-web.png';
        if (mainBar) mainBar.classList.remove('hover');
      } else {
        if (mainBar) mainBar.style.backgroundColor = 'transparent';
      }
    });
  });

  // Position behaviour - controla la posición de dropdowns al hacer scroll
  window.addEventListener('scroll', debounce(() => {
    const dropdowns = Array.from(document.querySelectorAll('.dropdown-menu'));
    homePositionParams(dropdowns);
  }, 100));


 /*--------------------------------------------------------------
    Menu pantalla mobile
  ----------------------------------------------------------------*/

  // Añade flecha a items mobile
  const mobileSpanTemplate = document.createElement('span');
  mobileSpanTemplate.innerHTML = '<img src="/wp-content/uploads/2024/09/arrow-short-right.png" alt="icono-arrow-right" class="menu-mobile-arrow">';
  document.querySelectorAll('li[class*="menu-mobile-item"]').forEach(li => {
    li.appendChild(mobileSpanTemplate.cloneNode(true));
  });

  let lastIndex = null;

  // Click en la flecha del mobile menu
  document.querySelectorAll('.menu-mobile-arrow').forEach(el => {
    el.addEventListener('click', (event) => {
      event.preventDefault();
      // Simula .mobile_menu_bar click si existe
      const mobileBar = document.querySelector('.mobile_menu_bar');
      if (mobileBar) mobileBar.click();

      const arrowElement = event.target.closest('li');
      const index = getStringInClassNameList(arrowElement, 'li-');
      lastIndex = index;
      const contentTabs = document.querySelectorAll('.content-tab');
      const tab = contentTabs[index];
      if (tab) {
        tab.classList.remove('tab-closed');
        tab.classList.add('tab-open');
      }
    });
  });

  document.querySelectorAll('.submenu-arrow').forEach(el => {
    el.addEventListener('click', () => {
      const contentTabs = document.querySelectorAll('.content-tab');
      const tab = contentTabs[lastIndex];
      if (tab) {
        tab.classList.remove('tab-open');
        tab.classList.add('tab-closed');
      }
    });
  });

  document.querySelectorAll('.mobile_menu_bar').forEach(el => {
    el.addEventListener('click', () => {
      const contentTabs = document.querySelectorAll('.content-tab');
      const tab = contentTabs[lastIndex];
      if (tab) {
        tab.classList.remove('tab-open');
        tab.classList.add('tab-closed');
      }
    });
  });

  // onClassChange utility using MutationObserver
  function onClassChange(selectorOrElement, cb) {
    const nodes = typeof selectorOrElement === 'string'
      ? document.querySelectorAll(selectorOrElement)
      : (selectorOrElement instanceof Node ? [selectorOrElement] : selectorOrElement);

    nodes.forEach(el => {
      new MutationObserver(mutations => {
        mutations.forEach(mutation => cb && cb(mutation.target, mutation.target.className));
      }).observe(el, { attributes: true, attributeFilter: ['class'] });
    });
  }

  onClassChange('.mobile_nav', (el, newClass) => {
    const isHome = document.body.classList.contains('home');
    const isOpened = newClass.includes('opened');

    if (isHome && isOpened) {
      mobileTabsBehaviour.isHomeIsOpen();
    } else if (isHome && !isOpened) {
      mobileTabsBehaviour.isHomeIsNotOpen();
    } else if (!isHome && isOpened) {
      mobileTabsBehaviour.isNotHomeIsOpen();
    } else if (!isHome && !isOpened) {
      mobileTabsBehaviour.isNotHomeIsNotOpen();
    }
  });


  /*--------------------------------------------------------------
    Nav tabs: Controla un sistema de tabs
  ----------------------------------------------------------------*/

  document.querySelectorAll('.tab-content').forEach(el => el.style.display = 'none');

  document.querySelectorAll('.tab-item').forEach(item => {
    item.addEventListener('click', function() {
      document.querySelectorAll('.tab-item').forEach(i => i.classList.remove('nav-item-active'));
      this.classList.add('nav-item-active');
      const tabId = this.id.replace('-item', '-content');
      document.querySelectorAll('.tab-content').forEach(c => c.style.display = 'none');
      document.querySelectorAll('.' + tabId).forEach(c => c.style.display = 'block');
    });
  });

  if (isParameterByName('tab')) {
    const tabName = getParameterByName('tab');
    const tabItem = document.getElementById(tabName + '-item');
    const tabContentClass = tabName + '-content';
    if (tabItem) tabItem.classList.add('nav-item-active');
    document.querySelectorAll('.' + tabContentClass).forEach(c => c.style.display = 'block');
  } else {
    const firstTabItem = document.querySelector('.tab-item');
    if (firstTabItem) {
      firstTabItem.classList.add('nav-item-active');
      firstTabItem.click();
    }
  }


  /* Toggle image src utility */
  const toggleImgSrc = (el) => {
    const tmp = el.getAttribute('src');
    el.setAttribute('src', el.getAttribute('toggle-src'));
    el.setAttribute('toggle-src', tmp);
  };

  const getClassWithPrefix = (element, prefix) => {
    const classList = element.className ? element.className.split(' ') : [];
    for (let className of classList) {
      if (className.startsWith(prefix)) {
        return { matchingClass: true, nonMatchingPart: className.slice(prefix.length) };
      }
    }
    return { matchingClass: false };
  };

  document.querySelectorAll('[id*="toggle-trigger"]').forEach(el => {
    el.addEventListener('click', function() {
      const classStringObject = getClassWithPrefix(this, 'parent-background-color');
      if (classStringObject.matchingClass) {
        this.parentNode.classList.toggle('background-parent-active' + classStringObject.nonMatchingPart);
      }

      const selector = this.id.replace('trigger', 'target');
      const squareImage = this.querySelector('.square-form-img');
      if (squareImage && squareImage.getAttribute('toggle-src')) toggleImgSrc(squareImage);

      const target = document.getElementById(selector);
      if (target) {
        target.style.display = (getComputedStyle(target).display === 'none') ? '' : 'none';
      }
    });
  });


  /* Gestiona la visibilidad de las secciones de proyectos. Navegación y filtrado */
  const projectMenuItems = document.getElementById('project-items');
  if (isInPage(projectMenuItems)) {
    const navTabs = document.querySelectorAll('.projects-nav-item');
    projectMenuItems.addEventListener('click', (event) => {
      const projectItems = document.querySelectorAll('.list-block');
      const ongoing = event.target.closest('#ongoing-projects-item');
      const finished = event.target.closest('#finished-projects-item');
      if (ongoing && !ongoing.classList.contains('nav-item-active')) {
        navTabs.forEach(tab => tab.classList.toggle('nav-item-active'));
        projectItems.forEach(item => {
          item.classList.contains('ongoing-project') ? item.classList.remove('hide-element') : item.classList.add('hide-element');
        });
      }
      if (finished && !finished.classList.contains('nav-item-active')) {
        navTabs.forEach(tab => tab.classList.toggle('nav-item-active'));
        projectItems.forEach(item => {
          item.classList.contains('finished-project') ? item.classList.remove('hide-element') : item.classList.add('hide-element');
        });
      }
    });
  }


  /* Formas y efectos en imagenes */
  const squareContainer = document.querySelector('.img-square-container');
  const newsSection = document.querySelector('.section-news');
  const sdgIcon = document.querySelector('.sdg-icon');
  if (isInPage(squareContainer)) addSquareFormToImg();
  if (isInPage(newsSection)) addRectangleFormToImg();
  if (isInPage(sdgIcon)) addSdgIconsToImg();

  function addRectangleFormToImg() {
    document.querySelectorAll('.section-news').forEach(section => {
      const articles = section.querySelectorAll('article');
      articles.forEach(element => {
        const category = getCategoryFromElement(element);
        const image = element.querySelector('img');
        if (image) image.insertAdjacentHTML('beforebegin', '<div class="rectangle"><span class="rectangle-text">' + category + '</span></div>');
      });
    });
  }

  function addSdgIconsToImg() {
    const imageContainer = document.querySelectorAll('.sdg-icon');
    const sdgIcons = ['sdg-03-en', 'sdg-07-en', 'sdg-11-en', 'sdg-09-en', 'sdg-13-en'];
    imageContainer.forEach(image => {
      sdgIcons.forEach(icon => {
        if (image.classList.contains(icon)) {
          const img = document.createElement('img');
          img.src = '/wp-content/uploads/2025/10/' + icon + '.png';
          img.alt = icon;
          img.className = 'sdg-icon-img';
          image.appendChild(img);
        }
      });
    });
  }

  function addSquareFormToImg() {
    document.querySelectorAll('[class*="img-square"].square-form-cross').forEach(el => el.insertAdjacentHTML('beforeend', '<img src="/wp-content/uploads/2025/02/cross-bg-transparent.png"  toggle-src="/wp-content/uploads/2025/03/dash-bg-transparent.png" alt="square-cross-form" class="square-form-img">'));
    document.querySelectorAll('[class*="img-square"].img-category-container img').forEach(img => img.insertAdjacentHTML('afterend', '<img src="/wp-content/uploads/2025/02/cross-bg-transparent.png"  toggle-src="/wp-content/uploads/2025/03/dash-bg-transparent.png" alt="square-cross-form" class="square-form-img">'));
    document.querySelectorAll('[class*="img-square"].square-form-arrow-left').forEach(el => el.insertAdjacentHTML('beforeend', '<img src="/wp-content/uploads/2025/02/left-arrow-bg-transparent.png" alt="square-arrow-form" class="square-form-img">'));
  }

  function getStringInClassNameList(element, keyWord) {
    if (!element) return '';
    const classNameList = element.className ? element.className.split(' ') : [];
    let catchedString = '';
    for (const value of classNameList) {
      if (value.includes(keyWord)) {
        const classText = value.split('-');
        catchedString = classText[classText.length - 1];
      }
    }
    return catchedString;
  }

  function getCategoryFromElement(element) {
    const newsCategories = ['in depth', 'research', 'innovation' , 'institutional' , 'a fondo' , 'investigación', 'innovación', 'institucional', 'a fons', 'recerca' , 'innovació', 'institucional', 'coffe talk', 'conferencias', 'conferences', 'conferències', 'congress', 'congrés', 'congreso', 'seminars', 'seminarios', 'seminaris', 'workshops', 'thesis defense', 'defensa de tesis', 'defensa de tesi'];
    let findCategory = 'Uncategorized';
    const categoryLinks = element.querySelectorAll('p.post-meta a');
    categoryLinks.forEach(link => {
      const category = link.textContent.trim();
      if (newsCategories.includes(category.toLowerCase())) findCategory = category;
    });
    return findCategory;
  }

  document.querySelectorAll('.flecha-container-move-down').forEach(el => el.addEventListener('click', () => {
    const intro = document.getElementById('intro');
    if (intro) window.scrollTo({ top: intro.offsetTop, behavior: 'smooth' });
  }));

  /*--------------------------------------------------------------
   News y events: Navegación y filtrado.
  ----------------------------------------------------------------*/

  const newsPage = document.getElementById('news-blog');
  if (isInPage(newsPage)) {
    const allEvents = document.querySelectorAll('article');
    const resetButton = document.getElementById('refresh-filter');
    const applyButton = document.getElementById('apply-filter');
    const fromDateInput = document.getElementById('start-date');
    const toDateInput = document.getElementById('end-date');
    const publicationFilter = document.getElementById('publication');
    const paginationContainer = document.getElementById('pagination-container');
    const itemsPerPage = 10;
    let currentPage = 1;

    const displayPaginatedEvents = (filteredEvents) => {
      allEvents.forEach(event => event.style.display = 'none');
      const start = (currentPage - 1) * itemsPerPage;
      const end = start + itemsPerPage;
      filteredEvents.slice(start, end).forEach(event => event.style.display = 'block');
    };

    const paginateEvents = (filteredEvents) => {
      const totalPages = Math.ceil(filteredEvents.length / itemsPerPage);
      paginationContainer.innerHTML = '';
      if (totalPages <= 1) {
        displayPaginatedEvents(filteredEvents);
        return;
      }
      if (currentPage > 1) {
        const prevButton = document.createElement('button'); prevButton.textContent = '«'; prevButton.classList.add('pagination-button'); prevButton.addEventListener('click', () => { currentPage = Math.max(1, currentPage - 1); paginateEvents(filteredEvents); }); paginationContainer.appendChild(prevButton);
      }
      const firstButton = document.createElement('button'); firstButton.textContent = '1'; firstButton.classList.add('pagination-button'); if (currentPage === 1) firstButton.classList.add('active'); firstButton.addEventListener('click', () => { currentPage = 1; paginateEvents(filteredEvents); }); paginationContainer.appendChild(firstButton);
      if (currentPage !== 1 && currentPage !== totalPages) { const activeButton = document.createElement('button'); activeButton.textContent = currentPage; activeButton.classList.add('pagination-button', 'active'); activeButton.disabled = true; paginationContainer.appendChild(activeButton); }
      if (totalPages > 1) { const lastButton = document.createElement('button'); lastButton.textContent = totalPages; lastButton.classList.add('pagination-button'); if (currentPage === totalPages) lastButton.classList.add('active'); lastButton.addEventListener('click', () => { currentPage = totalPages; paginateEvents(filteredEvents); }); paginationContainer.appendChild(lastButton); }
      if (currentPage < totalPages) { const nextButton = document.createElement('button'); nextButton.textContent = '»'; nextButton.classList.add('pagination-button'); nextButton.addEventListener('click', () => { currentPage = Math.min(totalPages, currentPage + 1); paginateEvents(filteredEvents); }); paginationContainer.appendChild(nextButton); }
      displayPaginatedEvents(filteredEvents);
    };

    const filterEvents = () => {
      const selectedType = publicationFilter ? publicationFilter.value.toLowerCase() : '';
      const fromDate = fromDateInput && fromDateInput.value ? new Date(fromDateInput.value).getTime() / 1000 : null;
      const toDate = toDateInput && toDateInput.value ? new Date(toDateInput.value).getTime() / 1000 : null;
      const searchQueryEl = document.getElementById('search-box');
      const searchQuery = searchQueryEl ? searchQueryEl.value.toLowerCase() : '';

      const filteredEvents = Array.from(allEvents).filter(event => {
        let eventDate = null;
        let eventType = true;
        if (newsPage.classList.contains('section-events')) {
          eventDate = parseInt(event.querySelector('.date').getAttribute('date'), 10);
          eventType = (event.querySelector('span.rectangle-text').textContent.toLowerCase() === selectedType) || selectedType === '';
        } else {
          const published = event.querySelector('.published');
          if (published) {
            let d = published.textContent.trim().split(' ')[0].replace(/\\/g, '-');
            eventDate = new Date(d).getTime() / 1000;
          }
          eventType = (getCategoryFromElement(event).toLowerCase() === selectedType) || selectedType === '';
        }
        const dateInRange = (!fromDate || eventDate >= fromDate) && (!toDate || eventDate <= toDate);
        const matchesSearch = searchQuery === '' || event.textContent.toLowerCase().includes(searchQuery);
        return eventType && dateInRange && matchesSearch;
      });

      const totalPages = Math.ceil(filteredEvents.length / itemsPerPage);
      if (currentPage > totalPages) currentPage = totalPages || 1;
      paginateEvents(filteredEvents);
    };

    if (resetButton) resetButton.addEventListener('click', () => {
      if (fromDateInput) fromDateInput.value = '';
      if (toDateInput) toDateInput.value = '';
      if (publicationFilter) publicationFilter.value = publicationFilter.options[0].value;
      const sb = document.getElementById('search-box'); if (sb) sb.value = '';
      currentPage = 1; filterEvents();
    });

    if (applyButton) applyButton.addEventListener('click', () => { currentPage = 1; filterEvents(); });

    filterEvents();
  }

  // FILTRO DE TESIS
  const tesisPage = document.getElementById('theses-container');

  function parseIsoDate(dateStr) { if (!dateStr) return null; return new Date(dateStr); }

  function filterReadingsByDate() {
    const startDateInput = document.getElementById('start-date');
    const endDateInput = document.getElementById('end-date');
    const startDateStr = startDateInput ? startDateInput.value : '';
    const endDateStr = endDateInput ? endDateInput.value : '';
    if (!startDateStr && !endDateStr) { resetFilter(); return; }
    const startDate = startDateStr ? parseIsoDate(startDateStr) : null;
    let endDate = endDateStr ? parseIsoDate(endDateStr) : null;
    if (endDate) endDate.setDate(endDate.getDate() + 1);

    document.querySelectorAll('.theses-list').forEach(dlElement => {
      let readingDate = null;
      const readingDt = dlElement.querySelector('.list-item dt');
      if (readingDt && readingDt.textContent.trim() === 'Reading:') {
        const readingDd = readingDt.nextElementSibling;
        if (readingDd) {
          const readingDateStr = readingDd.textContent.trim();
          const parts = readingDateStr.split('/');
          readingDate = new Date(parts[2], parts[1] - 1, parts[0]);
        }
      }
      let shouldShow = false;
      if (readingDate) {
        const readingTime = readingDate.getTime();
        const isAfterStart = startDate ? readingTime >= startDate.getTime() : true;
        const isBeforeEnd = endDate ? readingTime < endDate.getTime() : true;
        shouldShow = isAfterStart && isBeforeEnd;
      } else { shouldShow = false; }
      dlElement.style.display = shouldShow ? '' : 'none';
    });
  }

  function resetFilter() {
    const sb = document.getElementById('searchBox'); if (sb) sb.value = '';
    const sd = document.getElementById('start-date'); if (sd) sd.value = '';
    const ed = document.getElementById('end-date'); if (ed) ed.value = '';
    document.querySelectorAll('.theses-list').forEach(dl => dl.style.display = '');
  }

  if (isInPage(tesisPage)) {
    const resetBtn = document.getElementById('refresh-filter');
    const applyBtn = document.getElementById('apply-filter');
    if (resetBtn) resetBtn.addEventListener('click', resetFilter);
    if (applyBtn) applyBtn.addEventListener('click', filterReadingsByDate);
  }

  /* Imagen cabecera home random */
  const headerImageRow = document.querySelector('.home-header-image');
  const randomHeaderImage = () => {
    const images = ['/wp-content/uploads/2025/05/header1.jpg','/wp-content/uploads/2025/05/header2dark.jpg','/wp-content/uploads/2025/05/header3.jpg'];
    const randomIndex = Math.floor(Math.random() * images.length);
    const headerImage = document.querySelector('.home-header-image');
    if (headerImage) headerImage.style.backgroundImage = 'url(' + images[randomIndex] + ')';
  };
  if (isInPage(headerImageRow)) randomHeaderImage();

  /* Funcion de busqueda */
  let debounceTimer = null;
  const searchBox = document.getElementById('searchBox');
  if (searchBox) {
    searchBox.addEventListener('input', function() {
      const searchArea = document.querySelector('.blog-area article') ? document.querySelectorAll('.blog-area article') : document.querySelectorAll('.list-block, .staff-item, .theses-list');
      clearTimeout(debounceTimer);
      const self = this;
      debounceTimer = setTimeout(() => {
        const query = self.value.toLowerCase();
        searchArea.forEach(node => {
          const text = node.textContent.toLowerCase();
          node.style.display = text.includes(query) ? 'block' : 'none';
        });
      }, 300);
    });
  }

  const refreshFilterBtn = document.getElementById('refresh-filter');
  if (refreshFilterBtn) {
    refreshFilterBtn.addEventListener('click', () => {
      const sb = document.getElementById('searchBox'); if (sb) sb.value = '';
      const searchArea = document.querySelector('.blog-area') ? document.querySelectorAll('.blog-area article') : document.querySelectorAll('.list-block');
      searchArea.forEach(node => node.style.display = 'block');
    });
  }

  /* Funciones Auxiliares */
  const debounce = (func, wait) => {
    let timeout;
    return function(...args) { clearTimeout(timeout); timeout = setTimeout(() => func.apply(this, args), wait); };
  };

  function isInPage(node) { return node === document.body ? false : (node ? document.body.contains(node) : false); }

  function getParameterByName(name, url = window.location.href) { name = name.replace(/[\[\]]/g, "\\$&"); const regex = new RegExp("[?&]" + name + "(=([^&#]*)|&|#|$)"); const results = regex.exec(url); if (!results) return null; if (!results[2]) return ''; return decodeURIComponent(results[2].replace(/\+/g, " ")); }

  function isParameterByName(name) { const regex = new RegExp('[?&]' + name + '='); return regex.test(window.location.href); }

  /* homePositionParams */
  const homePositionParams = (dropdowns) => {
    const topBar = document.getElementById('top-bar');
    const mainBarEl = document.getElementById('main-bar');
    if (!topBar || !mainBarEl) return;
    const coords = topBar.getBoundingClientRect();
    let dropdownsTopPosition = (coords.top < -40) ? '104px' : '144px';
    dropdowns.forEach(el => { el.style.top = dropdownsTopPosition; });
  };

  /* mobileTabsBehaviour */
  const mobileTabsBehaviour = {
    isHomeIsOpen: () => {
      const el = document.querySelector('#page-container .mobile-menu');
      if (el) { el.style.position = 'fixed'; el.style.backgroundColor = '#004996'; }
    },
    isHomeIsNotOpen: () => {
      const el = document.querySelector('#page-container .mobile-menu');
      if (el) { el.style.position = 'relative'; el.style.backgroundColor = 'transparent'; }
    },
    isNotHomeIsOpen: () => {
      if (logoImg) logoImg.src = '/wp-content/uploads/2024/09/logo-blanco-cimne-web.png';
      const btn = document.querySelector('.mobile-menu button.et_pb_menu__search-button'); if (btn) { btn.style.border = '#fff solid 1px'; btn.style.color = '#fff'; }
      const el = document.querySelector('#page-container .mobile-menu'); if (el) el.style.backgroundColor = '#004996';
    },
    isNotHomeIsNotOpen: () => {
      if (logoImg) logoImg.src = '/wp-content/uploads/2024/09/logo-color-cimne-web.png';
      const btn = document.querySelector('.mobile-menu button.et_pb_menu__search-button'); if (btn) { btn.style.border = '#004996 solid 1px'; btn.style.color = '#004996'; }
      const el = document.querySelector('#page-container .mobile-menu'); if (el) el.style.backgroundColor = '#fff';
    }
  };

  /* Igualar la altura en el módulo del blog en rejilla */
  // Observa cambios y recalcula alturas
  document.querySelectorAll('.et_blog_grid_equal_height').forEach(blog => {
    const observer = new MutationObserver(() => { equalise_articles(blog); });
    observer.observe(blog, { subtree: true, childList: true });
    equalise_articles(blog);
  });

  function equalise_articles(blog) {
    const articles = blog.querySelectorAll('article');
    const heights = Array.from(articles).map(article => {
      let h = 0;
      const imgContainer = article.querySelector('.et_pb_image_container, .et_main_video_container');
      if (imgContainer) h += imgContainer.offsetHeight;
      const title = article.querySelector('.entry-title'); if (title) h += title.offsetHeight;
      const meta = article.querySelector('.post-meta'); if (meta) h += meta.offsetHeight;
      const content = article.querySelector('.post-content'); if (content) h += content.offsetHeight;
      return h;
    });
    const max_height = heights.length ? Math.max(...heights) : 0;
    articles.forEach(a => a.style.height = max_height + 'px');
  }

  // imagesLoaded replacement: returns Promise that resolves when images inside el are loaded
  function imagesLoaded(el) {
    const imgs = Array.from(el.querySelectorAll('img[src]')).filter(i => i.getAttribute('src').trim() !== '');
    if (!imgs.length) return Promise.resolve();
    return Promise.all(imgs.map(img => new Promise(res => { if (img.complete) return res(); img.onload = img.onerror = res; })));
  }

});
