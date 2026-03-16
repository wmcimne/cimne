
  
/*----------------------------------------------------------------------------------------
                MENU DESKTOP:
    Prevent a flash of tab content before JS has had a chance to wrap it into dropdowns.
    This hides tabs immediately (even before DOMContentLoaded) and relies on the JS dropdown
    behavior to show them when appropriate.
------------------------------------------------------------------------------------------*/

const _desktopMenuTabsPreHide = document.querySelectorAll('.desktop-menu-tab');
_desktopMenuTabsPreHide.forEach((tab) => {
  console.log('Pre-hiding desktop menu tab:', tab);
  // Keep tabs off-screen until they are attached to a dropdown.
  tab.style.left = '-9999px';
});

document.addEventListener('DOMContentLoaded', () => {
  const desktopMenuItems = document.querySelectorAll('#menu-desktop-main .menu-item');
  const desktopMenuTabs = document.querySelectorAll('.desktop-menu-tab');

  // If there are more tabs than menu items, we only attach up to the number of items.
  const count = Math.min(desktopMenuItems.length, desktopMenuTabs.length);

  const setOpen = (menuItem, dropdown, isOpen) => {
    // If there is a pending close, cancel it while opening.
    if (dropdown._closeTimer) {
      clearTimeout(dropdown._closeTimer);
      dropdown._closeTimer = null;
    }

    dropdown.classList.toggle('open', isOpen);
    menuItem.classList.toggle('dropdown-open', isOpen);
    dropdown.style.display = isOpen ? '' : 'none';
    dropdown.setAttribute('aria-hidden', String(!isOpen));

    const tab = dropdown.querySelector('.desktop-menu-tab');
    if (tab) {
      tab.style.left = isOpen ? '0' : '-9999px';
    }

    const link = menuItem.querySelector('a');
    if (link) {
      link.setAttribute('aria-expanded', String(isOpen));
    }
  };

  const scheduleClose = (menuItem, dropdown, delay = 250) => {
    if (dropdown._closeTimer) return;
    dropdown._closeTimer = setTimeout(() => {
      setOpen(menuItem, dropdown, false);
      dropdown._closeTimer = null;
    }, delay);
  };

  const createDropdown = (tabContent) => {
    const dropdown = document.createElement('div');
    dropdown.classList.add('desktop-menu-dropdown');
    dropdown.setAttribute('aria-hidden', 'true');
    dropdown.style.display = 'none';
    dropdown.appendChild(tabContent);
    return dropdown;
  };

  for (let i = 0; i < count; i++) {
    const menuItem = desktopMenuItems[i];
    const tabContent = desktopMenuTabs[i];

    // Wrap the tab content in a dropdown container for styling.
    const dropdown = createDropdown(tabContent);

    menuItem.appendChild(dropdown);

    // Hover behavior (desktop)
    menuItem.addEventListener('mouseenter', () => setOpen(menuItem, dropdown, true));
    menuItem.addEventListener('mouseleave', () => scheduleClose(menuItem, dropdown));

    // Keep open when the mouse moves between the item and the dropdown.
    dropdown.addEventListener('mouseenter', () => setOpen(menuItem, dropdown, true));
    dropdown.addEventListener('mouseleave', () => scheduleClose(menuItem, dropdown));

    // Click behavior (touch / toggle)
    menuItem.addEventListener('click', (event) => {
      // Ignore clicks inside the dropdown content
      if (event.target.closest('.desktop-menu-dropdown')) return;

      const link = menuItem.querySelector('a');
      const isOpen = dropdown.classList.contains('open');

      // If the menu item has a real link, prevent navigation only when opening.
      const href = link ? link.getAttribute('href') : null;
      const isRealLink = href && href !== '#' && !href.startsWith('javascript');

      if (isRealLink && isOpen) {
        return; // allow navigation when already open
      }

      event.preventDefault();
      setOpen(menuItem, dropdown, !isOpen);
    });
  }
});



jQuery(function($){

  

  /*--------------------------------------------------------------
    Menu pantalla de escritorio
  ----------------------------------------------------------------*/

  // $(function() {
  //   // Inserta los dropdowns creados a los items del menú primer nivel
  //   $('.dropdown-menu').each(function (i) {
  //     i = i + 1;
  //     let $dropdown = $('.dropdown-menu-' + i);
  //     let $mainMenuItem = $('.first-level-' + i + '>a');

  //     $dropdown.insertAfter($mainMenuItem);
  //   });
    
  
  //   if (!$('body').hasClass('home')) {
  //     // console.log('Is home');

  //     $('#main-bar').hover(
  //       function() {
  //         // console.log('hover');
  //         $('.et_pb_menu__logo img').attr('src', '/wp-content/uploads/2024/09/logo-blanco-cimne-web.png');
  //         $( this ).addClass( "hover" );
  //       }, function() {
  //         // console.log('no hover');
  //         $('.et_pb_menu__logo img').attr('src', '/wp-content/uploads/2024/09/logo-color-cimne-web.png'); 
  //         $( this ).removeClass( "hover" );
  //       }
  //     );

  //     $('li.first-level').hover(function() {
  //       $('.et_pb_menu__logo img').attr('src', '/wp-content/uploads/2024/09/logo-blanco-cimne-web.png');
  //         $('#main-bar' ).addClass( "hover" );
        
  //     })
  //   } else {
  //     // console.log('Is not home');

  //     $('li.first-level').hover(
  //       function() {
  //         //console.log('hover');
  //         $('#main-bar').css('background-color', '#004996');
  //       }, function() {
  //         //console.log('no hover');
  //         $('#main-bar').css('background-color', 'transparent');
  //       }
  //     );
  //   }

  //   // Position behaviour - Controla la ocultación de la barra superior del menú y la posición de los dropdowns al hacer scroll  

  //   //homePositionParams(dropdowns);

  //   window.onscroll = debounce(function () {
  //     const dropdowns = document.querySelectorAll('.dropdown-menu');
  //     homePositionParams(dropdowns);
	  
  //     //$('body').hasClass('home') ? homePositionParams(dropdowns) : noHomePositionParams(dropdowns);
  //   }, 100);
  // });

 /*--------------------------------------------------------------
    Menu pantalla mobile
  ----------------------------------------------------------------*/
  

  $(function() {

    let span = document.createElement('span');
    span.innerHTML = '<img src="/wp-content/uploads/2024/09/arrow-short-right.png" alt="icono-arrow-right" class="menu-mobile-arrow">';
  
    $('li[class*="menu-mobile-item"]').append(span); //menu-mobile-item-jobs li-4

    let index, arrowElement;
    
    // Muestra el submenú con efecto desplazamiento lateral
    $('.menu-mobile-arrow').click(function (event) {

      event.preventDefault(); 
      $('.mobile_menu_bar').click();

      arrowElement = event.target.parentNode.parentNode;
      index = getStringInClassNameList(arrowElement, 'li-');

      $('.content-tab').eq(index).animate({ left: "0px", top: "80px" });
    });

    // Oculta el submenú con efecto desplazamiento lateral
    $('.submenu-arrow').click(function () {
      $('.content-tab').eq(index).animate({ left: "1000px", top: "80px" });
    });
    $('.mobile_menu_bar').click(function () {
      $('.content-tab').eq(index).animate({ left: "1000px", top: "80px" });
    });

    // Método que dispa un evento cuando se cambia la clase de un elemento usando jQuery
    $.fn.onClassChange = function (cb) {
      return $(this).each((_, el) => {
        new MutationObserver(mutations => {
          mutations.forEach(mutation => cb && cb(mutation.target, mutation.target.className));
        }).observe(el, {
          attributes: true,
          attributeFilter: ['class'] // solo escucha para cambios del atributo 'class'
        });
      });
    }

    // Control del estado del elemento de menú cuando cambia la clase opened/closed
    $(".mobile_nav").onClassChange((el, newClass) => {

      let isHome = $('body').hasClass('home');
      let isOpened = newClass.includes('opened');

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
  });



  /*--------------------------------------------------------------
    Nav tabs: Controla un sistema de tabs
  ----------------------------------------------------------------
    Para no usar DIVI tabs module
  
    - xxxx-item : id of the tab item
    - xxxx-content : class of the tab content
  
    Where:
  
    - each menu item needs to have a class 'tab-item'
    - each tab content needs to have a class 'tab-content'

    * La url para referenciar una tab es: ?tab=xxxx
    ejemplo: ?tab=publications
    iu-nav-tab-2 iu-nav-tab-3 iu-nav-tab-4 iu-nav-tab-5 iu-nav-tab-6 iu-nav-tab-7 iu-nav-tab-8
    

  */
  $('.tab-content').hide();

    // Add click event to tab items
    $('.tab-item').click(function() {
      // Remove active class from all tab items
      $('.tab-item').removeClass('nav-item-active');
      // Add active class to the clicked tab item
      $(this).addClass('nav-item-active');
  
      // Get the ID of the clicked tab item
      let tabId = $(this).attr('id').replace('-item', '-content');
      console.log('tabId: ' + tabId);
  
      // Hide all tab contents
      $('.tab-content').hide();
      // Show the corresponding tab content
      console.log(`.${tabId}`);
      $(`.${tabId}`).show();
    });


  if(isParameterByName('tab')) {
    let tabName = getParameterByName('tab');
    console.log('tabName: ' + tabName);
    let tabItem = tabName + '-item';
    console.log('tabItem: ' + tabItem);
    let tabContent = tabName + '-content';
    console.log('tabContent: ' + tabContent);
    
    $('#' + tabItem).addClass('nav-item-active');
    $('.' + tabContent).show();
    
  } else {

    console.log('tab not found');
    // Hide all tab contents except the first one
    $('.tab-item').first().addClass('nav-item-active').click();

  }


  /*------------------------------------------------------------------------------
    Gestiona la visibilidad de las secciones de proyectos. Navegación y filtrado

    *Adaptar a otras listas como publicaciones, etc
  -------------------------------------------------------------------------------*/
  
  let projectMenuItems =  document.getElementById('project-items');

  if (isInPage(projectMenuItems)) {
    
    const navTabs = document.querySelectorAll('.projects-nav-item');

    projectMenuItems.addEventListener('click', (event) => {

      let projectItems =  document.querySelectorAll('.list-block');

      if (event.target.closest('#ongoing-projects-item') && !event.target.closest('#ongoing-projects-item').classList.contains('nav-item-active')) {

        navTabs.forEach(tab => tab.classList.toggle('nav-item-active'));

        projectItems.forEach(item => {
          item.classList.contains('ongoing-project') ?  item.classList.remove('hide-element') : item.classList.add('hide-element');
        });
      }

      if (event.target.closest("#finished-projects-item") && !event.target.closest('#finished-projects-item').classList.contains('nav-item-active')) {

        navTabs.forEach(tab => tab.classList.toggle('nav-item-active'));

        projectItems.forEach(item => {
          item.classList.contains('finished-project') ? item.classList.remove('hide-element') : item.classList.add('hide-element');
        });
      }
    }); 
  } else {
    console.log('projectMenuItems not found');
  }
 
  /*--------------------------------------------------------------
    Toggle tabs: 
  ----------------------------------------------------------------
      - Añadir 
          toggle-triger-xxxxx en el disparador
          toggle-target-xxxxx en el objetivo
  */

  
  
  /* Intercambia el src de la img al hacer toggle. 'toggle-src' tiene que añadirse previamente en img */
  const toggleImgSrc = (jQueryObj) => {
    tmp = jQueryObj.attr('src');
    jQueryObj.attr('src', jQueryObj.attr('toggle-src'));
    jQueryObj.attr('toggle-src', tmp);
  }
  
  /* Captura la clase de un elemento si los primeros caracteres coinciden con los que se pasan a la función.*/
  const getClassWithPrefix = (element, prefix) => {
    const classList = element.className.split(' ');
    for (let className of classList) {
      if (className.startsWith(prefix)) {
        return {
          matchingClass: true,
          nonMatchingPart: className.slice(prefix.length)
        };
      }
    }
    return false;
  };

  $('[id*="toggle-trigger"]').click(function () {
    console.log(getClassWithPrefix(this, "parent-background-color"));

    const classStringObject = getClassWithPrefix(this, "parent-background-color");
    //check estos logs
    console.log('classStringObject: ' + classStringObject);
    console.log('classStringObject.matchingClass: ' + classStringObject.matchingClass);
    console.log('classStringObject.nonMatchingPart: ' + classStringObject.nonMatchingPart);
    // check estas variables
    let parentBackgroudColorChange = getClassWithPrefix(this, "background-parent").matchingClass;
    let backgroundParentColor = getClassWithPrefix(this, "background-parent").nonMatchingPart;

    classStringObject.matchingClass ? $(this).parent().toggleClass(`background-parent-active${classStringObject.nonMatchingPart}`) : console.log('background-parent not found');

    let selector = $(this).attr('id').replace('trigger', 'target');
    let squareImage = $(this).find('.square-form-img');

    squareImage.attr('toggle-src') ? toggleImgSrc(squareImage) : console.log('toggle-src not found');
    $('#' + selector).toggle("slow");
  });		





  /*--------------------------------------------------------------
    Formas y efectos en imagenes
  ----------------------------------------------------------------*/

  $(function() {
    const squareContainer = document.querySelector('.img-square-container');
    const newsSection = document.querySelector('.section-news'); 
    const sdgIcon = document.querySelector('.sdg-icon');

     // revisar este condicional 
    isInPage(squareContainer) ? addSquareFormToImg() : console.log('squareContainer not found');
    isInPage(newsSection) ? addRectangleFormToImg() : console.log('newsSection not found');
    isInPage(sdgIcon) ? addSdgIconsToImg() : console.log('sdgIcon not found');
  });

  const addRectangleFormToImg = () => {

    let container = document.querySelectorAll('.section-news');
    let article, category, image;

    container.forEach(function (section) {
      //console.log(section);
      article = section.querySelectorAll('article');
      article.forEach(function (element) {
        //console.log(element);
          category = getCategoryFromElement(element);
          //console.log('category: ' + category);
          image = element.querySelector('img');
          $(image).before('<div class="rectangle"><span class="rectangle-text">' + category + '</span></div>');
      });
    });
  }

  const addSdgIconsToImg = () => {
    console.log('addSdgIconsToImg');
    let imageContainer = document.querySelectorAll('.sdg-icon');
    let sdgIcons = ["sdg-03-en", "sdg-07-en", "sdg-11-en", "sdg-09-en", "sdg-13-en" ];

    imageContainer.forEach(function (image) {
      sdgIcons.forEach(function (icon) {
        let hasClass = image.classList.contains(icon);
        if (hasClass) {
          let img = document.createElement('img');
          img.src = '/wp-content/uploads/2025/10/' + icon + '.png';
          img.alt = icon;
          img.className = 'sdg-icon-img';
          image.append(img);
          //console.log('img: ' + img.innerHTML);
          //console.log('image: ' + image.innerHTML);
        }
      });
    });
  }

  const addSquareFormToImg = () => {
    //check if square-form-cross class exists in the website

    $('[class*="img-square"].square-form-cross').append('<img src="/wp-content/uploads/2025/02/cross-bg-transparent.png"  toggle-src="/wp-content/uploads/2025/03/dash-bg-transparent.png" alt="square-cross-form" class="square-form-img">');
    $('[class*="img-square"].img-category-container img').after('<img src="/wp-content/uploads/2025/02/cross-bg-transparent.png"  toggle-src="/wp-content/uploads/2025/03/dash-bg-transparent.png" alt="square-cross-form" class="square-form-img">');
    $('[class*="img-square"].square-form-arrow-left').append('<img src="/wp-content/uploads/2025/02/left-arrow-bg-transparent.png" alt="square-arrow-form" class="square-form-img">');
  }

  const getStringInClassNameList = (element, keyWord) => {

    console.log(`getStringInClassNameList: ${element} keyword: ${keyWord}`);

    let classNameList = element.className.split(' ');
    let catchedString = '';
    // console.log('classNameList: ' + classNameList);
    // console.log('Language is: ' + document.documentElement.lang);

    for (value of classNameList) {
      
      if (value.includes(keyWord)) {
        classText = value.split('-');
        catchedString = classText[classText.length - 1];
        // console.log(`catchedString: ${catchedString}`);
      }
    }
    return catchedString;
  }

   const getCategoryFromElement = (element) => {

    const newsCategories = ['in depth', 'research', 'innovation' , 'institutional' , 'a fondo' , 'investigación', 'innovación', 'institucional', 'a fons', 'recerca' , 'innovació', 'institucional', 'coffe talk', 'conferencias', 'conferences', 'conferències', 'congress', 'congrés', 'congreso', 'seminars', 'seminarios', 'seminaris', 'workshops', 'thesis defense', 'defensa de tesis', 'defensa de tesi'];
    let findCategory = 'Uncategorized';
    let category = '';
    const categoryLinks = element.querySelectorAll('p.post-meta a');

    categoryLinks.forEach(link => {
      category = link.textContent.trim();
      //console.log(`find this category: ${category}`);
      if (newsCategories.includes(category.toLowerCase())) {
        //console.log(`Found category: ${category}`);
        findCategory = category;
      }    
    });

    return findCategory;
  }

  $(".flecha-container-move-down").click(function() {
    console.log('flecha-container-move-dow clicked');
    $('html, body').scrollTop($("#intro").offset().top);
  });



  /*--------------------------------------------------------------
   News y events: Navegación y filtrado.
  ----------------------------------------------------------------*/

  let newsPage = document.getElementById('news-blog');

  if (isInPage(newsPage)) {

    const allEvents = document.querySelectorAll("article");
    const resetButton = document.getElementById("refresh-filter");
    const applyButton = document.getElementById("apply-filter");
    let fromDateInput = document.getElementById("start-date");
    let toDateInput = document.getElementById("end-date");
    let publicationFilter = document.getElementById("publication");
    const paginationContainer = document.getElementById("pagination-container");
    const itemsPerPage = 10; // Número de eventos por página
    let currentPage = 1;



    const paginateEvents = (filteredEvents) => {
      const totalPages = Math.ceil(filteredEvents.length / itemsPerPage);
      paginationContainer.innerHTML = "";

      if (totalPages <= 1) {
        displayPaginatedEvents(filteredEvents);
        return;
      }

      // Página -
      if (currentPage > 1) {
        const prevButton = document.createElement("button");
        prevButton.textContent = "«";
        prevButton.classList.add("pagination-button");
        prevButton.addEventListener("click", () => {
          currentPage = Math.max(1, currentPage - 1);
          paginateEvents(filteredEvents);
        });
        paginationContainer.appendChild(prevButton);
      }

      // Primera página
      const firstButton = document.createElement("button");
      firstButton.textContent = "1";
      firstButton.classList.add("pagination-button");
      if (currentPage === 1) firstButton.classList.add("active");
      firstButton.addEventListener("click", () => {
        currentPage = 1;
        paginateEvents(filteredEvents);
      });
      paginationContainer.appendChild(firstButton);

      // Página activa (si no es primera ni última)
      if (currentPage !== 1 && currentPage !== totalPages) {
        const activeButton = document.createElement("button");
        activeButton.textContent = currentPage;
        activeButton.classList.add("pagination-button", "active");
        activeButton.disabled = true; // Deshabilitada para mostrar solo la página activa
        paginationContainer.appendChild(activeButton);
      }

      // Última página
      if (totalPages > 1) {
        const lastButton = document.createElement("button");
        lastButton.textContent = totalPages;
        lastButton.classList.add("pagination-button");
        if (currentPage === totalPages) lastButton.classList.add("active");
        lastButton.addEventListener("click", () => {
          currentPage = totalPages;
          paginateEvents(filteredEvents);
        });
        paginationContainer.appendChild(lastButton);
      }

      // Página +
      if (currentPage < totalPages) {
        const nextButton = document.createElement("button");
        nextButton.textContent = "»";
        nextButton.classList.add("pagination-button");
        nextButton.addEventListener("click", () => {
          currentPage = Math.min(totalPages, currentPage + 1);
          paginateEvents(filteredEvents);
        });
        paginationContainer.appendChild(nextButton);
      }

      displayPaginatedEvents(filteredEvents);
    };

    const displayPaginatedEvents = (filteredEvents) => {
      allEvents.forEach(event => event.style.display = "none");
      const start = (currentPage - 1) * itemsPerPage;
      const end = start + itemsPerPage;
      filteredEvents.slice(start, end).forEach(event => event.style.display = "block");
    };

    const filterEvents = () => {
      const now = Date.now() / 1000; // Fecha actual en timestamp UNIX
      //const selectedType = publicationFilter.value.toLowerCase().replace(" ", "-");
      const selectedType = publicationFilter.value.toLowerCase();
      const fromDate = fromDateInput.value ? new Date(fromDateInput.value).getTime() / 1000 : null;
      const toDate = toDateInput.value ? new Date(toDateInput.value).getTime() / 1000 : null;
      const searchQuery = $('#search-box').val().toLowerCase(); // Obtener el valor del campo de búsqueda
      let eventDate, eventType;

      const filteredEvents = Array.from(allEvents).filter(event => {
        //console.log("Es evento? " + newsPage.classList.contains("section-events"));
        if (newsPage.classList.contains("section-events")) {
          console.log("Filtrando eventos");
          eventDate = parseInt(event.querySelector(".date").getAttribute("date"), 10);
          eventType = event.querySelector('span.rectangle-text').textContent.toLowerCase() === selectedType || selectedType === "";
          // console.log('eventDate: ' + eventDate);
          // console.log('eventType: ' + eventType);
        } else {
          eventDate = event.querySelector(".published").textContent.trim().split(" ")[0].replace(/\//g, "-");
          eventDate = new Date(eventDate).getTime() / 1000;
          //console.log('Categoria: ' + getCategoryFromElement(event));
          eventType = getCategoryFromElement(event).toLocaleLowerCase() === selectedType || selectedType === "";
        }

        const dateInRange = (!fromDate || eventDate >= fromDate) && (!toDate || eventDate <= toDate);
        const matchesSearch = searchQuery === "" || event.textContent.toLowerCase().includes(searchQuery); // Verificar si coincide con la búsqueda

        return eventType && dateInRange && matchesSearch;
      });

      // Reset currentPage if out of bounds
      const totalPages = Math.ceil(filteredEvents.length / itemsPerPage);
      if (currentPage > totalPages) currentPage = totalPages || 1;

      paginateEvents(filteredEvents);
    };

    resetButton.addEventListener("click", () => {
      fromDateInput.value = "";
      toDateInput.value = "";
      publicationFilter.value = publicationFilter.options[0].value;
      $('#search-box').val('');
      currentPage = 1;
      filterEvents();
    });

    applyButton.addEventListener("click", () => {
      currentPage = 1;
      filterEvents();
    });

    // Aplicar filtro al cargar la página
    filterEvents();
  }

	
// -----------------------------------------------------------------------------
// FILTRO DE TESIS
// -----------------------------------------------------------------------------

// Función auxiliar para parsear el formato DD/MM/YYYY a un objeto Date para comparación
// El formato del HTML es DD/MM/YYYY, pero el input[type='date'] devuelve YYYY-MM-DD.
// Esta función maneja el formato YYYY-MM-DD (ISO) devuelto por el input date.
function parseIsoDate(dateStr) {
    if (!dateStr) return null; // Retorna null si la cadena está vacía
    // new Date('YYYY-MM-DD') crea la fecha correctamente en la zona horaria local
    return new Date(dateStr);
}

// -----------------------------------------------------------------------------
// FUNCIÓN PRINCIPAL DE FILTRO
// -----------------------------------------------------------------------------
// 
// 

let tesisPage = document.getElementById('theses-container');

function filterReadingsByDate() {
	console.log("filterReadingsByDate");
    // 1. Obtener los valores de los campos de fecha del formulario
    const startDateInput = document.getElementById('start-date');
    const endDateInput = document.getElementById('end-date');

    const startDateStr = startDateInput.value;
    const endDateStr = endDateInput.value;

    // Si ambos campos están vacíos, no aplicamos filtro de fecha, solo mostramos todo.
    if (!startDateStr && !endDateStr) {
        // Llama a una función de reseteo si no hay fechas, para mostrar todos los elementos.
        resetFilter();
        return;
    }

    const startDate = startDateStr ? parseIsoDate(startDateStr) : null;
    let endDate = endDateStr ? parseIsoDate(endDateStr) : null;
	
	console.log(startDate, endDate);

    // Ajuste importante para incluir el día final: si hay fecha de fin, 
    // la ajustamos para que la comparación sea hasta el final del día.
    if (endDate) {
        // Sumamos un día a la fecha de fin y luego filtramos por < (menor que) este valor.
        endDate.setDate(endDate.getDate() + 1);
    }
    
    // 2. Seleccionar todos los contenedores de las tesis (los elementos <dl>)
    const thesisLists = document.querySelectorAll('.theses-list');

    thesisLists.forEach(dlElement => {
        let readingDateStr = '';
        let readingDate = null;
        let shouldShow = false;

        // Buscar el div.list-item que contiene la fecha de 'Reading'
        const readingDt = dlElement.querySelector('.list-item dt');
		console.log(readingDt);

        if (readingDt && readingDt.textContent.trim() === 'Reading:') {
            // El elemento <dd> inmediatamente siguiente contiene la fecha.
            const readingDd = readingDt.nextElementSibling;
			console.log(readingDd);
            if (readingDd) {
                // El formato en el HTML es DD/MM/YYYY, debemos parsearlo.
                readingDateStr = readingDd.textContent.trim();
                
                const parts = readingDateStr.split('/');
                // new Date(year, monthIndex, day) - monthIndex es 0-indexado.
                readingDate = new Date(parts[2], parts[1] - 1, parts[0]);
				console.log(readingDate);
            }
        }

        if (readingDate) {
            // 3. Comparar la fecha de lectura con el rango de fechas
            const readingTime = readingDate.getTime();
            
            const isAfterStart = startDate ? readingTime >= startDate.getTime() : true; // Si no hay fecha de inicio, siempre es 'después'.
            const isBeforeEnd = endDate ? readingTime < endDate.getTime() : true; // Si no hay fecha de fin, siempre es 'antes'.
            
            shouldShow = isAfterStart && isBeforeEnd;

        } else {
             // Si el elemento no tiene campo 'Reading', se oculta (o se maneja según la lógica de negocio, aquí asumimos ocultar)
             shouldShow = false;
        }

        // 4. Mostrar u ocultar el elemento <dl> (el conjunto completo de datos)
        dlElement.style.display = shouldShow ? '' : 'none';
    });
}

// -----------------------------------------------------------------------------
// FUNCIÓN PARA RESETEAR EL FILTRO
// -----------------------------------------------------------------------------

function resetFilter() {
	console.log("resetFilter");
    // Resetear los campos del formulario
    document.getElementById('searchBox').value = '';
    document.getElementById('start-date').value = '';
    document.getElementById('end-date').value = '';

    // Mostrar todos los elementos de la lista
    const thesisLists = document.querySelectorAll('.theses-list');
    thesisLists.forEach(dlElement => {
        dlElement.style.display = ''; // Restaura el display a su valor por defecto (muestra)
    });
}

// -----------------------------------------------------------------------------
// CONFIGURACIÓN DE LISTENERS
// -----------------------------------------------------------------------------

if (isInPage(tesisPage)) {
	
	const resetButton = document.getElementById("refresh-filter");
   	const applyButton = document.getElementById("apply-filter");
	
	resetButton.addEventListener("click", () => {
		resetFilter();
    });

     applyButton.addEventListener("click", () => {
      	filterReadingsByDate();
     });	
}

  /*--------------------------------------------------------------
    Imagen cabecera home random 
  ----------------------------------------------------------------*/
  // const headerImageRow = document.querySelector('.home-header-image');
  // const randomHeaderImage = () => {
  //   console.log('randomHeaderImage');
  //   let images = [
  //     '/wp-content/uploads/2025/05/header3-1.jpg',
  //     '/wp-content/uploads/2025/05/header2dark-1.jpg',
  //     '/wp-content/uploads/2025/05/header1-1.jpg'
  //   ];
  //   let randomIndex = Math.floor(Math.random() * images.length);
  //   let headerImage = document.querySelector('.home-header-image');
  //   headerImage.style.backgroundImage = 'url(' + images[randomIndex] + ')';
  // }
  // isInPage(headerImageRow) ? randomHeaderImage() : console.log('headerImageRow not found');
	
/*--------------------------------------------------------------
    Descarga de eventos en formato ICS 
  ----------------------------------------------------------------*/

  const downloadICSButton = document.getElementById("add-to-calendar-button");

  if (isInPage(downloadICSButton)) {

    downloadICSButton.addEventListener("click", function () {


      // Obtener datos con las rutas exactas que indicaste
      const titleEl = document.querySelector("#event-title .entry-title");
      const dateEl = document.querySelector("#event-date .et_pb_module_header span");
      const timeEl = document.querySelector("#event-time .et_pb_module_header span");
      const placeEl = document.querySelector("#event-place .et_pb_module_header span");

      const title = titleEl ? titleEl.textContent.trim() : "";
      const dateStr = dateEl ? dateEl.textContent.trim() : "";
      const timeStr = timeEl ? timeEl.textContent.trim() : "";
      const place = placeEl ? placeEl.textContent.trim() : "";

      // --- Parsear fecha DD/MM/YYYY ---
      function parseDateDDMMYYYY(s) {
        const parts = s.split("/").map(p => p.trim());
        if (parts.length !== 3) return null;
        const day = parseInt(parts[0], 10);
        const month = parseInt(parts[1], 10);
        const year = parseInt(parts[2], 10);
        if (
          Number.isNaN(day) || Number.isNaN(month) || Number.isNaN(year) ||
          day < 1 || day > 31 || month < 1 || month > 12 || year < 1
        ) return null;
        return { day, month, year };
      }

      // --- Convertir hora 12h (hh:mm am/pm) a objeto { hour, minute } en 24h ---
      function parseTime12h(s) {
        if (!s) return null;
        const parts = s.toLowerCase().trim().split(/\s+/); // ["12:00", "pm"]
        if (parts.length === 0) return null;
        let time = parts[0];
        let modifier = parts[1] || ""; // am/pm (puede venir separado o no)
        // Si viene como "12:00pm" sin espacio
        if (!modifier) {
          const m = time.match(/(am|pm)$/);
          if (m) {
            modifier = m[1];
            time = time.replace(/(am|pm)$/, "");
          }
        }
        const tm = time.split(":");
        if (tm.length < 2) return null;
        let hour = parseInt(tm[0], 10);
        let minute = parseInt(tm[1], 10);
        if (Number.isNaN(hour) || Number.isNaN(minute)) return null;
        if (modifier === "pm" && hour !== 12) hour += 12;
        if (modifier === "am" && hour === 12) hour = 0;
        if (hour < 0 || hour > 23 || minute < 0 || minute > 59) return null;
        return { hour, minute };
      }

      const dateParts = parseDateDDMMYYYY(dateStr);
      const timeParts = parseTime12h(timeStr);

      if (!dateParts) {
        console.error("Fecha inválida:", dateStr);
        alert("Formato de fecha inválido. Usa DD/MM/YYYY (ej. 09/12/2025).");
        return;
      }
      if (!timeParts) {
        console.error("Hora inválida:", timeStr);
        alert("Formato de hora inválido. Usa hh:mm am/pm (ej. 12:00 pm).");
        return;
      }

      // Crear Date en hora local usando componentes
      const start = new Date(
        dateParts.year,
        dateParts.month - 1, // monthIndex 0-11
        dateParts.day,
        timeParts.hour,
        timeParts.minute,
        0
      );

      if (isNaN(start.getTime())) {
        console.error("Fecha resultante inválida", start);
        alert("No se pudo crear la fecha/hora del evento. Revisa los valores.");
        return;
      }

      // Duración por defecto 1 hora (puedes cambiarlo)
      const end = new Date(start.getTime() + 60 * 60 * 1000);

      // Formatear fecha a YYYYMMDDTHHMMSSZ usando UTC (compatible ICS)
      function formatDateToICSUTC(d) {
        const pad = (n) => String(n).padStart(2, "0");
        return d.getUTCFullYear().toString() +
          pad(d.getUTCMonth() + 1) +
          pad(d.getUTCDate()) + "T" +
          pad(d.getUTCHours()) +
          pad(d.getUTCMinutes()) +
          pad(d.getUTCSeconds()) + "Z";
      }

      const dtstamp = formatDateToICSUTC(new Date());
      const dtstart = formatDateToICSUTC(start);
      const dtend = formatDateToICSUTC(end);

      const uid = `${Date.now()}-${Math.random().toString(36).slice(2)}@miapp`;

      const icsLines = [
        "BEGIN:VCALENDAR",
        "VERSION:2.0",
        "PRODID:-//MiApp//ES",
        "CALSCALE:GREGORIAN",
        "BEGIN:VEVENT",
        `UID:${uid}`,
        `DTSTAMP:${dtstamp}`,
        `DTSTART:${dtstart}`,
        `DTEND:${dtend}`,
        `SUMMARY:${escapeICSText(title)}`,
        `LOCATION:${escapeICSText(place)}`,
        "END:VEVENT",
        "END:VCALENDAR"
      ];

      const icsContent = icsLines.join("\r\n");

      const blob = new Blob([icsContent], { type: "text/calendar;charset=utf-8" });
      const link = document.createElement("a");
      link.href = URL.createObjectURL(blob);
      link.download = `${(title || "evento").replace(/\s+/g, "_")}.ics`;
      document.body.appendChild(link);
      link.click();
      link.remove();

      // Escapar caracteres especiales en texto para ICS (comas, punto y coma, saltos de línea)
      function escapeICSText(text) {
        if (!text) return "";
        return text
          .replace(/\\/g, "\\\\")
          .replace(/\n/g, "\\n")
          .replace(/,/g, "\\,")
          .replace(/;/g, "\\;");
      }
    });
  }

  /*--------------------------------------------------------------
    Funcion de busqueda
  ----------------------------------------------------------------*/

  let debounceTimer; // Variable para el temporizador

  $('#searchBox').on('input', function() {
      console.log('input change');


      const searchArea = document.querySelector('.blog-area article') ? $('.blog-area article') : $('.list-block, .staff-item, .theses-list');
      clearTimeout(debounceTimer); // Cancelar el temporizador anterior
      let $this = $(this);
      debounceTimer = setTimeout(function() {
          let query = $this.val().toLowerCase();
          
          console.log('Búsqueda ejecutada con:', query);

          searchArea.each(function() {
              let item = $(this).text().toLowerCase();
              // console.log(item.includes(query));
              item.includes(query) ? $(this).css('display', 'block') : $(this).css('display', 'none');
          });
      }, 300); // Retraso de 300 ms
  });

  $('#refresh-filter').on('click', function() {
      // console.log('Reset button clicked');
      $('#searchBox').val(''); // Limpiar el campo de búsqueda
      const searchArea = document.querySelector('.blog-area') ? $('.blog-area') : $('.list-block');
      searchArea.each(function() {
          $(this).css('display', 'block'); // Mostrar todos los elementos
      });
  });


  /*--------------------------------------------------------------
    Funciones Auxiliares
  ----------------------------------------------------------------*/

  // Debounce function
  const debounce = (func, wait) => {
    let timeout;
    return function(...args) {
      clearTimeout(timeout);
      timeout = setTimeout(() => func.apply(this, args), wait);
    };
  };


  // busca si un elemento está en el dom
  function isInPage(node) {
    return node === document.body ? false : document.body.contains(node);
  }

  // trae el valor de un párametro de la url
  function getParameterByName(name, url=window.location.href) {
    name = name.replace(/[\[\]]/g, "\\$&");
    var regex = new RegExp("[?&]" + name + "(=([^&#]*)|&|#|$)"),
    results = regex.exec(url);
    if (!results) return null;
    if (!results[2]) return '';
    return decodeURIComponent(results[2].replace(/\+/g, " "));
  }

  // busca si un parámetro está en la url
  function isParameterByName(name) {
    let regex = new RegExp('[?&]' + name + '=');
    return regex.test(window.location.href);
  }
  
  // posición y comportamiento de la barra y dropdowns en función de si es home o no 

  /* 
     ---> REVISAR COMO INCLUIR LAS POSICIONES DE LAS BARRAS DE MENÚ 
     
  */
  const homePositionParams = (dropdowns) => {

    //console.log('homePositionParams');

    let topBar = document.getElementById('top-bar');
    let mainBar = document.getElementById('main-bar');
    let coords = topBar.getBoundingClientRect();
    //console.log('coords: ' + coords.top);
    // let topBarHeight = topBar.offsetHeight;
    // let mainBarHeight = mainBar.offsetHeight;
    // let dropdownsTopPosition = topBarHeight + mainBarHeight + coords.top + 'px';
    let dropdownsTopPosition = '';

    //console.log('dropdownsTopPosition: ' + dropdownsTopPosition);

    (coords.top < -40) ? dropdownsTopPosition = '104px' : dropdownsTopPosition = '144px';

    dropdowns.forEach((element) => {
      element.style.top = dropdownsTopPosition;
    })
  }
  

  // Update Mobile Menu Styles Features
  const mobileTabsBehaviour = {

    isHomeIsOpen: () => {
      console.log('mobileTabsBehaviour: isHomeIsOpen');
      
      $('#page-container .mobile-menu').css({'position' : 'fixed', 'background-color': '#004996'});
    },
    isHomeIsNotOpen: () => {
      console.log('mobileTabsBehaviour: isHomeIsNotOpen');

      $('#page-container .mobile-menu').css({'position' : 'relative','background-color': 'transparent'});
    },
    isNotHomeIsOpen: () => {
      console.log('mobileTabsBehaviour: isNotHomeIsOpen');

      $('.et_pb_menu__logo img').attr('src', '/wp-content/uploads/2024/09/logo-blanco-cimne-web.png');
      $('.mobile-menu button.et_pb_menu__search-button ').css({'border': '#fff solid 1px', 'color': '#fff'});
      $('#page-container .mobile-menu').css('background-color', '#004996');
    },
    isNotHomeIsNotOpen: () => {
      console.log('mobileTabsBehaviour: isNotHomeIsNotOpen');

      $('.et_pb_menu__logo img').attr('src', '/wp-content/uploads/2024/09/logo-color-cimne-web.png');
      $('.mobile-menu button.et_pb_menu__search-button ').css({'border': '#004996 solid 1px', 'color': '#004996'});
      $('#page-container .mobile-menu').css('background-color', '#fff');
    }
  }

   /*--------------------------------------------------------------
    Igualar la altura en el módulo del blog en rejilla
  ----------------------------------------------------------------*/


  $(document).ready(function() {

    $(window).resize(function() {
        $('.et_blog_grid_equal_height').each(function() {
            equalise_articles($(this));
        });
    });

    $('.et_blog_grid_equal_height').each(function() {
        var blog = $(this);

        equalise_articles($(this));

        var observer = new MutationObserver(function(mutations) {
            equalise_articles(blog);
        });
        
        var config = {
            subtree: true,
            childList: true 
        };

        observer.observe(blog[0], config);
    });

    function equalise_articles(blog) {
        console.log('equalise_articles called');
        var articles = blog.find('article');
        var heights = [];
        
        articles.each(function() {
            var height = 0;
            height += $(this).find('.et_pb_image_container, .et_main_video_container').outerHeight(true);
            height += $(this).find('.entry-title').outerHeight(true);
            height += $(this).find('.post-meta').outerHeight(true); 
            height += $(this).find('.post-content').outerHeight(true);    

            heights.push(height);
        });

        var max_height = Math.max.apply(Math,heights); 

        articles.each(function() {
            $(this).height(max_height);
        });
    }

    $(document).ajaxComplete(function() {
        $('.et_blog_grid_equal_height').imagesLoaded().then(function() {
            $('.et_blog_grid_equal_height').each(function(){
                equalise_articles($(this));
            });
        });
    });

    $.fn.imagesLoaded = function() {
        var $imgs = this.find('img[src!=""]');
        var dfds = [];

        if (!$imgs.length) {
            return $.Deferred().resolve().promise();
        }            

        $imgs.each(function(){
            var dfd = $.Deferred();
            dfds.push(dfd);
            var img = new Image();

            img.onload = function() {
                dfd.resolve();
            };

            img.onerror = function() {
                dfd.resolve(); 
            };

            img.src = this.src;
        });

        return $.when.apply($, dfds);
    }
  });

});


