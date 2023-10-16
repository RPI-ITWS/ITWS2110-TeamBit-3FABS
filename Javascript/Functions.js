function like(element) {
    const likedImage = "./images/liked.png";
    const unlikedImage = "./images/heart.png";

    element.src = element.classList.contains("active") ? unlikedImage : likedImage;
    element.classList.toggle("active");
}

/**
 * Toggles the visibility of the comment box.
 * @param {HTMLElement} elem - The element that triggered the function.
 */
function toggleCommentBox(elem) {
    const commentBox = elem.closest('.post').querySelector('.comment');
    commentBox.classList.toggle('hidden');
}