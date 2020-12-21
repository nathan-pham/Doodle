let urlParams = new URLSearchParams(window.location.search);
let type = urlParams.get("type");
let clicked = [];
let timer;

function query(route, data) {
    let form = new FormData();
    for(const [key, value] of Object.entries(data || {})) {
        form.append(key, value)
    }

    return fetch(route, {
        method: "POST",
        body: form
    })
}

type == "sites" ? manageSites() : manageImages();

function manageImages() {
    const grid = new Masonry(".image-results", {
        itemSelector: ".grid-item",
        columnWidth: 390,
        fitWidth: true,
        initLayout: false
    })

    const images = document.querySelectorAll(".pseudo-image");
    const parents = [];
    for(const pseudo of images) {
        const { src } = pseudo.dataset;
        const loader = new Image();
        parents.push(pseudo.parentNode);

        loader.addEventListener("load", () => {
            const image = document.createElement("img");
            image.src = loader.src;
            pseudo.replaceWith(image);

            clearTimeout(timer);
            
            timer = setTimeout(() => {
                grid.layout();
                parents.forEach((parent) => {
                    parent.style.opacity = 1;
                })
            }, 500);
        })

        loader.addEventListener("error", () => {
            parent.remove();
            query("api/update.php", {
                type: "images",
                src
            });
        })

        loader.src = src;
    }
}

function manageSites() {
    const titles = document.querySelectorAll("h3.title > a");
    for(const title of titles) {
        title.addEventListener("click", (e) => {
            let redirect = e.ctrlKey && e.metaKey && e.shiftKey
            if(redirect) {
                e.preventDefault();
            }

            let id = e.target.dataset.id;
            let url = e.target.href;
            
            if(clicked.includes(id)) {
                return false;
            }
            clicked.push(id);

            id ? incrementClick(id, url, redirect) : console.log("ERROR: data-id not found");
            
            return false;
        })
    }
}

function incrementClick(id, url, redirect) {
    query("api/update.php", {
        type: "sites",
        id
    });

    if(redirect) {
        window.location.href = url;
    }
}
