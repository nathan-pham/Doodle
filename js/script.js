let clicked = false;

function init() {
    const titles = document.querySelectorAll("h3.title > a");
    for(const title of titles) {
        title.addEventListener("click", function(e) {
            if(!e.ctrlKey && !e.metaKey && !e.shiftKey) {
                e.preventDefault();
            }

            let id = e.target.dataset.id;
            let url = e.target.href;

            id ? incrementClick(id, url) : console.log("ERROR: data-id not found");
            
            return false;
        })
    }
}

function incrementClick(id, url) {
    if(clicked) {
        return;
    }

    let form = new FormData();
    form.append("id", id);

    clicked = true;

    fetch("api/update.php", {
        method: "POST",
        body: form
    }).then(res => res.text());

    window.location.href = url;
}

window.addEventListener("load", init);