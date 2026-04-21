/* Cookie code */

function getCookie(name)
{
    const value = `; ${document.cookie}`;
    const parts = value.split(`; ${name}=`);
    if (parts.length === 2) return parts.pop().split(";").shift();
}

function setCookie(name, value)
{
    const date = new Date();
    date.setTime(date.getTime() + (400 * 24 * 60 * 60 * 1000)); // 400 days
    document.cookie = `${name}=${value};expires=${date.toUTCString()};path=/;SameSite=Strict`;
}

function deleteCookie(name)
{
    document.cookie = `${name}=;expires=Thu, 01 Jan 1970 00:00:00 UTC;path=/;SameSite=Strict`
}

/* Get and set timezone accordingly */
let tzOffset = (new Date).getTimezoneOffset();
if (tzOffset != getCookie("tz"))
{
    setCookie("tz", tzOffset);
    location.reload();
}

/* Menu code */

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
}, false);