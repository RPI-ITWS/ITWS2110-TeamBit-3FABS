

function like(element) {
    const likedImage = "./images/heart-white.png";
    const unlikedImage = "./images/heart-black.png";

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


const allPosts = [
    {
        title: "End of the Dither-Day",
        account: "@myfriend",
        accountLink: "./linktoaccount",
        thumbnails: "./images/sunset.png",
        postDate: "07/23/2010"
    },
    {
        title: "My New Friend",
        account: "@splatlands",
        accountLink: "./linktoaccount",
        thumbnails: "./images/newfriend.png",
        postDate: "09/08/2005"
    },
    {
        title: "I love HRT",
        account: "@chickoon",
        accountLink: "./linktoaccount",
        thumbnails: "./images/download.png",
        postDate: "03/16/2077"
    },
    {
        title: "Mimikyuuuu",
        account: "@chii28",
        accountLink: "./linktoaccount",
        thumbnails: "./images/mimikyu.png",
        postDate: "08/31/2018"
    },
    {
        title: "BORF DOG YEAAAAAAAAAAAAAAAA",
        account: "@thisfieldisrequired",
        accountLink: "./linktoaccount",
        thumbnails: "./images/borf.png",
        postDate: "22/33/2000"
    },
]


const displayedPosts = document.querySelector(".contentFeed");


const generatePosts = () => {

    const shuffledList = allPosts.sort((a,b) => 0.5 - Math.random()).slice(0.3);
    shuffledList.forEach((post) => {
        
        const title = document.createElement("h1");
        title.classList.add("postTitle");
        title.innerHTML = post.title;

        const thumbnailLink = document.createElement("figure");
        const thumbnail = document.createElement("img");
        thumbnail.classList.add("postImage");
        thumbnail.src = post.thumbnails;
        thumbnailLink.append(thumbnail);

        //footer
        const footers = document.createElement("footer");
        footers.classList.add("postFooter");
        const likes = document.createElement("img");
        likes.className = "like";
        likes.src = "./images/heart-black.png";
        likes.onclick = function(){like(this)};
        
        const comment = document.createElement("img");
        comment.classList.add("like");
        comment.src = "./images/comment.png";
        comment.onclick = function(){toggleCommentBox(this)};
        
        const account = document.createElement("p");
        account.classList.add("tag");
        const accountLink = document.createElement("a");
        accountLink.href = post.accountLink;
        accountLink.innerHTML = post.account;
        account.append(accountLink);

        const date = document.createElement("p");
        date.classList.add("postDate");
        date.innerHTML = post.postDate;

        const share = document.createElement("img");
        share.classList.add("like");
        share.src = "./images/share.png";
        share.onclick = function(){share()};

        footers.append(likes);
        footers.append(comment);
        footers.append(account);
        footers.append(date);
        footers.append(share);

        const relatedPost = document.createElement("div");
        relatedPost.classList.add("post");

        relatedPost.appendChild(title);
        relatedPost.appendChild(thumbnailLink);
        relatedPost.appendChild(footers);
        

        displayedPosts.appendChild(relatedPost);
    });
}

generatePosts();

// Set default values
const defaultThreshold = 50;
const defaultContrast = 50;

// Reset functions
document.getElementById('resetThreshold').addEventListener('click', function () {
  document.getElementById('threshold').value = defaultThreshold;
  ditherCall();
});

document.getElementById('resetContrast').addEventListener('click', function () {
  document.getElementById('contrast').value = defaultContrast;
  ditherCall();
});

// jQuery click event for an interactive element
$('.interactive-element').on('click', function () {
  alert('You clicked the interactive element!');
});