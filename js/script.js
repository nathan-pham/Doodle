let urlParams = new URLSearchParams(window.location.search);
let type = urlParams.get("type") || "sites";
let term = urlParams.get("term");
let clicked = [];
let page = 1;

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
    loadImages();
    
    const container = document.querySelector(".image-results");
    window.addEventListener("scroll", async () => {
        const {scrollHeight, scrollTop, clientHeight} = document.documentElement
        if(scrollTop + clientHeight > scrollHeight - 5) {
            const html = await query("api/scroll.php", {
                page: page++,
                term
            }).then(res => res.text());
    
            if(html) {
                container.innerHTML += html;
                loadImages();
            }
        }
    })
}

function loadImages() {
    const images = document.querySelectorAll(".pseudo-image");

    for(const pseudo of images) {
        const { src } = pseudo.dataset;
        const loader = new Image();

        loader.addEventListener("load", () => {
            const image = document.createElement("img");
            image.src = loader.src;
            pseudo.parentNode.style.opacity = 1;
            pseudo.replaceWith(image);
            image.addEventListener("click", previewImage);
        })

        loader.addEventListener("error", () => {
            parent.remove();
            query("api/broken.php", {
                src
            });
        })

        loader.src = src;
    }
}

function previewImage(e) {
    const parent = e.target.parentNode;
    const a = parent.querySelector("a");

    for(const active of document.querySelectorAll(".active")) {
        active.classList.remove("active");
    }

    parent.classList.add("active");

    const preview = document.createElement("div");
    preview.className = "preview";
    preview.innerHTML = `
        <div class="preview-content">
            <div class="image-wrapper">
                <img src="${e.target.src}" />
                <button>✕</button>
            </div>
            <hr />
            <h3>
                <a href="${a.href}" target="_blank">${a.querySelector("h3").textContent}</a>
            </h3>
            <p>Image may be subject to copyright.</p>
            </div>
        </div> 
    `;

    preview.querySelector("button").addEventListener("click", () => {
        preview.remove();
        parent.classList.remove("active");
    })

    document.body.appendChild(preview);
    incrementImageClick(e.target.src);
}

function incrementImageClick(src) {
    query("api/update.php", {
        type: "images",
        src
    });
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

            id ? incrementSiteClick(id, url, redirect) : console.log("ERROR: data-id not found");
            
            return false;
        })
    }
}

function incrementSiteClick(id, url, redirect) {
    query("api/update.php", {
        type: "sites",
        id
    });

    if(redirect) {
        window.location.href = url;
    }
}