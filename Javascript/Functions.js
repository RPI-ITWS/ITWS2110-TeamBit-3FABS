function like(element) {
    element.src = element.bln ? "./images/heart.png" : "./images/liked.png";
    element.bln = !element.bln;
    element.classList.toggle("active")
}

function toggleCommentBox(elem){
    elem.parentElement.parentElement.querySelector("#commentbox").classList.toggle("hidden")
}