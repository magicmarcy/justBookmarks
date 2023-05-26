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

function saveScroll() {
    localStorage.setItem("scrollPosition", document.getElementById("content-left").scrollTop);
}
