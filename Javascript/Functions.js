

function like(element, id, isComment = false) {
    const likedImage = "/project/images/heart-white.png";
    const unlikedImage = "/project/images/heart-black.png";

    const isLiked = element.classList.contains("active");
    const params = {
        direction: isLiked ? "0" : "1",
    };
    params[isComment ? "comment_id" : "post_id"] = id;

    fetch("/project/api_like.php?" + new URLSearchParams(params)).then(function () {
        element.src = isLiked ? unlikedImage : likedImage;
        element.classList.toggle("active");
    })
}

/**
 * Toggles the visibility of the comment box.
 * @param {HTMLElement} elem - The element that triggered the function.
 */
function toggleCommentBox(elem) {
    const commentBox = elem.closest('.post').querySelector('.comment');
    commentBox.classList.toggle('hidden');
}


// Set default values
const defaultThreshold = 50;
const defaultContrast = 50;

// Reset functions
const resetThresholdElement = document.getElementById('resetThreshold');
if (resetThresholdElement) {
    resetThresholdElement.addEventListener('click', function () {
        document.getElementById('threshold').value = defaultThreshold;
        ditherCall();
    });
}

const resetContrastElement = document.getElementById('resetContrast');
if (resetContrastElement) {
    resetContrastElement.addEventListener('click', function () {
        document.getElementById('contrast').value = defaultContrast;
        ditherCall();
    });
}

// jQuery click event for an interactive element
$('.interactive-element').on('click', function () {
    alert('You clicked the interactive element!');
});

$(document).on('click', '.new-comment-form-button', function () {
    const targetId = $(this).data('for');
    $("#" + targetId).toggleClass('open');
})