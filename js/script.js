function init() {
    const titles = document.querySelectorAll("h3.title > a");
    for(const title of titles) {
        title.addEventListener("click", function(e) {
            if(!e.ctrlKey && !e.metaKey && !e.shiftKey) {
                e.preventDefault();
            }

            let url = e.target.href;
            

            return false;
        })
    }
}

async function increaseLinks(id, url) {

}

window.addEventListener("load", init);