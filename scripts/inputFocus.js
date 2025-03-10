document.body.style.height = document.body.scrollHeight + window.innerHeight/2 + 'px';


let pathName = window.location.pathname;
let category = localStorage.getItem('category')
if (pathName.includes('add_')) {
    if(!pathName.includes(category)){
        window.location.href = `add_${category}.php`;
    }
}