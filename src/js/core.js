function toggleMenu(id, event)
{
    document.getElementById(id).classList.toggle("open");
    event.preventDefault();
    event.stopPropagation();
}

document.addEventListener("click", function(e)
{
    let menu = document.querySelector(".menu.open");
    if (menu)
    {
        menu.classList.remove("open");
    }
});