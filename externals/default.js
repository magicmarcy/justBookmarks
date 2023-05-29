$(document).ready(function(){
    $('#search').keyup(function() {
        const query = $(this).val();
        $.ajax({
            url: 'search.php',
            method: 'POST',
            data: { query: query },
            success: function(response) {
                $('#search-results').html(response);
            }
        });
    });

    // Default set the focus into the searchfield
    let searchbar = document.getElementById('search');

    if (searchbar) {
        searchbar.focus();
    }
});

document.onkeyup = function(e) {
    if (e.altKey && e.which === 87) {
        document.getElementById('search').focus();
    }

    if (e.which === 27) {
        deleteSearchData();
    }
};

function onkeypressed(evt, input) {
    const code = evt.charCode || evt.keyCode;
    if (code === 27) {
        input.value = '';
    }
}

function focusFirstEntry(evt) {
    const code = evt.charCode || evt.keyCode;

    if (code === 40) {
        evt.preventDefault();

        document.getElementById('search-results').getElementsByTagName('a')[0].focus();
    }
}

function fucusNextEntry(evt, input) {
    const code = evt.charCode || evt.keyCode;

    // Pfeil nach oben
    const isUp = code === 38;

    // Pfeil nach unten
    const isDown = code === 40;

    if (isUp || isDown) {
        evt.preventDefault();

        const list = document.getElementById('search-results').getElementsByTagName('a');

        let indexOfActiveElement = -1;

        for (let i = 0; i < list.length; i++) {
            if (list[i] === document.activeElement) {
                indexOfActiveElement = i;
            }
        }

        if (isUp && indexOfActiveElement !== -1) {
            if (indexOfActiveElement - 1 >= 0) {
                indexOfActiveElement -= 1;
            } else {
                indexOfActiveElement = list.length - 1;
            }
        } else if (isDown && indexOfActiveElement !== -1 && indexOfActiveElement + 1 < list.length) {
            indexOfActiveElement += 1;
        } else {
            indexOfActiveElement = 0;
        }

        list[indexOfActiveElement].focus();
    }

    closeAndDeleteSearchData(evt);
}

function closeAndDeleteSearchData(evt) {
    const code = evt.charCode || evt.keyCode;

    if (code === 27) {
        deleteSearchData();
        document.getElementById('search').focus();
    }
}

function showAddBookmarkModal() {
    let element = document.getElementById('bookmark-modal');
    let overlay = document.getElementById('overlay');

    element.style.display = 'inline-block';
    overlay.style.display = 'inline-block';
}

function hideAddBookmarkModal() {
    let element = document.getElementById('bookmark-modal');
    let overlay = document.getElementById('overlay');

    element.style.display = 'none';
    overlay.style.display = 'none';

    clearInputs();
}

function showBookmarkletModal() {
    let element = document.getElementById('bookmarkletModal');
    let overlay = document.getElementById('overlay');

    element.style.display = 'inline-block';
    overlay.style.display = 'inline-block';
}

function hideBookmarkletModal() {
    let element = document.getElementById('bookmarkletModal');
    let overlay = document.getElementById('overlay');

    element.style.display = 'none';
    overlay.style.display = 'none';
}

$(document).keydown(function(e) {
    // ESCAPE key pressed
    if (e.keyCode === 27) {
        let element = document.getElementById('bookmark-modal');
        let overlay = document.getElementById('overlay');

        if (element) {
            element.style.display = 'none';
            overlay.style.display = 'none';
        }

        clearInputs();
    }
});

function hideOverlay() {
    let overlay = document.getElementById('overlay');
    let element = document.getElementById('bookmark-modal');

    element.style.display = 'none';
    overlay.style.display = 'none';

    clearInputs();
}

function validateNewBookmark(element) {
    let submitButton = document.getElementById('submitNewBookmark');

    if (element && element.value !== '') {
        submitButton.removeAttribute('disabled');
    }
}

function clearInputs() {
    let name = document.getElementById('name');
    let url = document.getElementById('url');
    let tags = document.getElementById('tags');

    if (name) {
        name.value = '';
    }

    if (url) {
        url.value = '';
    }

    if (tags) {
        tags.value = '';
    }

    let submitButton = document.getElementById('submitNewBookmark');
    submitButton.setAttribute('disabled', 'disabled');
}

function deleteSearchData() {
    document.getElementById('search').value = "";
    dontshowResultBox();
}

function dontshowResultBox() {
    document.getElementById('search-results').style.display = 'none';
}

function showResultBox() {
    document.getElementById('search-results').style.display = 'block';
}

function saveScroll() {
    localStorage.setItem("scrollPosition", document.getElementById("content-left").scrollTop);
}
